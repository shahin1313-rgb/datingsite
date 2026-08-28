<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private const MESSAGES_PER_PAGE = 50;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $authUser = $request->user();
        $userId = $authUser->id;

        /*
         * A conversation may remain in the database after a block or an
         * account-status change. Only load conversations whose other party
         * is still discoverable by the signed-in user.
         */
        $discoverableUserIds = fn (): Builder =>
            User::query()
                ->discoverableBy($authUser)
                ->select('users.id');

        $contacts = Message::query()
            ->where(
                function (Builder $query) use (
                    $userId,
                    $discoverableUserIds
                ): void {
                    $query
                        ->where(
                            function (Builder $sent) use (
                                $userId,
                                $discoverableUserIds
                            ): void {
                                $sent
                                    ->where('sender_id', $userId)
                                    ->whereIn(
                                        'receiver_id',
                                        $discoverableUserIds()
                                    );
                            }
                        )
                        ->orWhere(
                            function (Builder $received) use (
                                $userId,
                                $discoverableUserIds
                            ): void {
                                $received
                                    ->where('receiver_id', $userId)
                                    ->whereIn(
                                        'sender_id',
                                        $discoverableUserIds()
                                    );
                            }
                        );
                }
            )
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(
                fn (Message $message): int =>
                    (int) ((int) $message->sender_id === (int) $userId
                        ? $message->receiver_id
                        : $message->sender_id)
            );

        $unreadCounts = [];

        foreach ($contacts as $contactUserId => $messages) {
            $unreadCounts[$contactUserId] = $messages
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();
        }

        return view(
            'messages.index',
            compact('contacts', 'unreadCounts', 'authUser')
        );
    }

    public function show(Request $request, User $user)
    {
        $authUser = $request->user();
        $recipient = $this->discoverableRecipient(
            $authUser,
            (int) $user->getKey()
        );

        /*
         * Authorize the recipient before changing read state. This prevents
         * an IDOR request from having any side effect.
         */
        Message::query()
            ->where('sender_id', $recipient->id)
            ->where('receiver_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::query()
            ->where(
                function (Builder $query) use (
                    $authUser,
                    $recipient
                ): void {
                    $query
                        ->where('sender_id', $authUser->id)
                        ->where('receiver_id', $recipient->id);
                }
            )
            ->orWhere(
                function (Builder $query) use (
                    $authUser,
                    $recipient
                ): void {
                    $query
                        ->where('sender_id', $recipient->id)
                        ->where('receiver_id', $authUser->id);
                }
            )
            ->orderByDesc('id')
            ->simplePaginate(self::MESSAGES_PER_PAGE)
            ->withQueryString();

        /*
         * The query fetches the newest page efficiently. Reverse only the
         * current page so chat bubbles are still rendered chronologically.
         */
        $messages->setCollection(
            $messages->getCollection()->reverse()->values()
        );

        return view('messages.show', [
            'user' => $recipient,
            'messages' => $messages,
            'selectedUser' => $recipient,
            'canViewPrivateMessages' => $authUser->isPremium(),
        ]);
    }

    public function store(Request $request)
    {
        $sender = $request->user();

        $validated = $request->validate([
            'receiver_id' => ['required', 'integer'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        /*
         * The same query protects nonexistent, blocked, banned, unverified,
         * admin and self IDs. findOrFail deliberately returns 404 so the
         * endpoint cannot be used as an account-enumeration oracle.
         */
        $receiver = $this->discoverableRecipient(
            $sender,
            (int) $validated['receiver_id']
        );

        $messagesCount = Message::query()
            ->where(
                function (Builder $query) use (
                    $sender,
                    $receiver
                ): void {
                    $query
                        ->where('sender_id', $sender->id)
                        ->where('receiver_id', $receiver->id);
                }
            )
            ->orWhere(
                function (Builder $query) use (
                    $sender,
                    $receiver
                ): void {
                    $query
                        ->where('sender_id', $receiver->id)
                        ->where('receiver_id', $sender->id);
                }
            )
            ->count();

        if ($messagesCount === 0) {
            Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $validated['message'],
                'status' => 'private',
            ]);

            return response()->json([
                'success' => true,
                'type' => 'FIRST_MESSAGE_PRIVATE',
            ]);
        }

        if (! $sender->isPremium() && ! $receiver->isPremium()) {
            return response()->json([
                'error' => 'PREMIUM_REQUIRED',
                'receiver_id' => $receiver->id,
            ], 402);
        }

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $validated['message'],
            'status' => 'sent',
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    private function discoverableRecipient(
        User $sender,
        int $receiverId
    ): User {
        return User::query()
            ->discoverableBy($sender)
            ->findOrFail($receiverId);
    }
}
