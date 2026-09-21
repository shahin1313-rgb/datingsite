<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $latestMessageIds = Message::query()
            ->selectRaw('MAX(id)')
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
            ->groupByRaw(
                'CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END',
                [$userId]
            );

        $contacts = Message::query()
            ->whereIn('id', $latestMessageIds)
            ->with(['sender', 'receiver'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $contactId = static fn (Message $message): int =>
                (int) ((int) $message->sender_id === (int) $userId
                    ? $message->receiver_id
                    : $message->sender_id);

        /*
         * Keep the historical view contract: conversation keys are the
         * other user's id, while the paginator still limits the query.
         */
        $contacts->setCollection(
            $contacts->getCollection()->keyBy($contactId)
        );

        $contactUserIds = $contacts->getCollection()
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->values();

        $queriedUnreadCounts = Message::query()
            ->where('receiver_id', $userId)
            ->whereIn('sender_id', $contactUserIds)
            ->whereNull('read_at')
            ->selectRaw('sender_id, COUNT(*) AS aggregate')
            ->groupBy('sender_id')
            ->pluck('aggregate', 'sender_id')
            ->map(fn ($count): int => (int) $count)
            ->all();

        /*
         * A visible conversation must also have an explicit zero after
         * all of its messages are read.
         */
        $unreadCounts = array_fill_keys(
            $contactUserIds->all(),
            0
        );

        foreach ($queriedUnreadCounts as $contactUserId => $count) {
            $unreadCounts[(int) $contactUserId] = $count;
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
        $unreadMessages = Message::query()
            ->where('sender_id', $recipient->id)
            ->where('receiver_id', $authUser->id)
            ->whereNull('read_at');

        /*
         * A private incoming message is rendered as a locked placeholder for
         * non-premium users. Do not acknowledge it until its content can
         * actually be displayed; otherwise unread badges and sender receipts
         * would incorrectly claim that the message was seen.
         */
        if (! $authUser->isPremium()) {
            $unreadMessages->where('status', '!=', 'private');
        }

        $unreadMessages->update(['read_at' => now()]);

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

        $result = DB::transaction(function () use (
            $sender,
            $receiver,
            $validated
        ): array {
            /*
             * The user rows are the stable lock for this pair. Messages do
             * not provide a row to lock before the first message exists.
             * Always lock in id order so opposite-direction requests cannot
             * acquire the same two locks in a different order and deadlock.
             */
            $participants = User::query()
                ->whereKey([$sender->id, $receiver->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (User $user): int => (int) $user->id);

            $lockedSender = $participants->get((int) $sender->id);
            $lockedReceiver = $participants->get((int) $receiver->id);

            abort_unless($lockedSender && $lockedReceiver, 404);

            $conversationExists = Message::query()
                ->where(
                    function (Builder $query) use (
                        $lockedSender,
                        $lockedReceiver
                    ): void {
                        $query
                            ->where('sender_id', $lockedSender->id)
                            ->where('receiver_id', $lockedReceiver->id);
                    }
                )
                ->orWhere(
                    function (Builder $query) use (
                        $lockedSender,
                        $lockedReceiver
                    ): void {
                        $query
                            ->where('sender_id', $lockedReceiver->id)
                            ->where('receiver_id', $lockedSender->id);
                    }
                )
                ->exists();

            if (! $conversationExists) {
                Message::create([
                    'sender_id' => $lockedSender->id,
                    'receiver_id' => $lockedReceiver->id,
                    'message' => $validated['message'],
                    'status' => 'private',
                ]);

                return [
                    'status' => 200,
                    'body' => [
                        'success' => true,
                        'type' => 'FIRST_MESSAGE_PRIVATE',
                    ],
                ];
            }

            if (
                ! $lockedSender->isPremium()
                && ! $lockedReceiver->isPremium()
            ) {
                return [
                    'status' => 402,
                    'body' => [
                        'error' => 'PREMIUM_REQUIRED',
                        'receiver_id' => $lockedReceiver->id,
                    ],
                ];
            }

            Message::create([
                'sender_id' => $lockedSender->id,
                'receiver_id' => $lockedReceiver->id,
                'message' => $validated['message'],
                'status' => 'sent',
            ]);

            return [
                'status' => 200,
                'body' => ['success' => true],
            ];
        }, 3);

        return response()->json($result['body'], $result['status']);
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
