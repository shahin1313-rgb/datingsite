<?php

namespace App\Http\Controllers;

use id;
use App\Models\User;
use App\Models\Message;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $userId = auth()->id();
        $authUser = Auth::user();
        $userId = $authUser->id;

        // Fetch users who sent messages to the logged-in user, along with the latest message
        $contacts = Message::where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->with('sender', 'receiver')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });

        // Count unread messages for each contact
        $unreadCounts = [];
        foreach ($contacts as $contactUserId => $messages) {
            $unreadCounts[$contactUserId] = $messages->where('receiver_id', $userId)->where('is_read', false)->count();
        }


        return view('messages.index', compact('contacts', 'unreadCounts', 'authUser'));
    }

    // Show messaging box for a specific user
    public function show(User $user)
    {
        // Get the authenticated user
        $authUser = Auth::user();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);


        // Fetch the profile of the selected user (or your own if needed)
        // $profile = User::where('user_id', $user->id)->first();
        $selectedUser = User::find($user);


        // Mark unread messages as read
        // Message::where('sender_id', $id)
        // ->where('receiver_id', $authId)
        // ->where('is_read', false)
        // ->update(['is_read' => true]);

        // Get messages between the authenticated user and the selected user
        $messages = Message::where(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $authUser->id)->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $authUser->id);
        })->orderBy('created_at', 'asc')->get();

        return view('messages.show', compact('user', 'messages', 'selectedUser'));
    }


    // Store a new message
  public function store(Request $request)
{
    $sender = Auth::user();

    $request->validate([
        'receiver_id' => 'required|exists:users,id|not_in:' . $sender->id,
        'message'     => 'required|string|max:1000',
    ]);

    $receiverId = (int) $request->receiver_id;


   

    // چک بلاک
    if ($sender->isBlockedBy($receiverId) || $sender->hasBlocked($receiverId)) {
        return back()->with('error', 'Cannot send message to this user.');
    }

    // آیا قبلاً بین این دو پیام بوده؟
    $alreadySentBefore = Message::where('sender_id', $sender->id)
        ->where('receiver_id', $receiverId)
        ->exists();

     // پیام دوم و بعدی → پرداخت
    if ($alreadySentBefore) {
        return response()->json([
            'error' => 'PAYMENT_REQUIRED',
            'message' => 'Unlock chat by upgrading your account.'
        ], 402);
    }
    // ذخیره پیام
    Message::create([
        'sender_id'   => $sender->id,
        'receiver_id' => $receiverId,
        'message'     => $request->message,
        'is_read'     => false,
    ]);

    return back()->with('success', 'Message sent successfully.');
}

}
    
