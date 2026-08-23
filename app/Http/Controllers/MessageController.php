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




//    public function store(Request $request)
// {
//     dd('STORE HIT');
// }
// public function store(Request $request)
// {
//     $sender = Auth::user();

//     $request->validate([
//         'receiver_id' => 'required|exists:users,id|not_in:' . $sender->id,
//         'message'     => 'required|string|max:1000',
//     ]);

//     $receiver = User::findOrFail($request->receiver_id);

//     // چک بلاک
//     if ($sender->isBlockedBy($receiver->id) || $sender->hasBlocked($receiver->id)) {
//         return response()->json([
//             'error' => 'BLOCKED'
//         ], 403);
//     }

//     // تعداد کل پیام‌ها بین دو طرف (دوطرفه)
//     $messagesCount = Message::where(function ($q) use ($sender, $receiver) {
//             $q->where('sender_id', $sender->id)
//               ->where('receiver_id', $receiver->id);
//         })
//         ->orWhere(function ($q) use ($sender, $receiver) {
//             $q->where('sender_id', $receiver->id)
//               ->where('receiver_id', $sender->id);
//         })
//         ->count();

//     /**
//      * 1️⃣ پیام اول → آزاد، فقط فرستنده می‌بیند
//      */
//     if ($messagesCount === 0) {

//         Message::create([
//             'sender_id'   => $sender->id,
//             'receiver_id' => $receiver->id,
//             'message'     => $request->message,
//             'status'      => 'private', // فقط فرستنده
//             'is_read'     => false,
//         ]);

//         return response()->json([
//             'success' => true,
//             'type'    => 'FIRST_MESSAGE_PRIVATE'
//         ]);
//     }

//     /**
//      * 2️⃣ پیام دوم به بعد → نیاز به پریمیوم یکی از دو طرف
//      */
//     if (! $sender->is_premium && ! $receiver->is_premium) {
//         return response()->json([
//             'error' => 'PREMIUM_REQUIRED',
//             'receiver_id' => $receiverId
//         ], 402);
//     }

//     /**
//      * 3️⃣ ارسال پیام عادی
//      */
//     Message::create([
//         'sender_id'   => $sender->id,
//         'receiver_id' => $receiver->id,
//         'message'     => $request->message,
//         'status'      => 'sent',
//         'is_read'     => false,
//     ]);

//     return response()->json([
//         'success' => true
//     ]);

// }
public function store(Request $request)
{
    $sender = Auth::user();

    // 1. اعتبارسنجی ورودی‌ها
    $request->validate([
        'receiver_id' => 'required|exists:users,id|not_in:' . $sender->id,
        'message'     => 'required|string|max:1000',
    ]);

    $receiver = User::findOrFail($request->receiver_id);

    // 2. چک کردن وضعیت بلاک
    // توجه: متدهای isBlockedBy و hasBlocked باید در مدل User شما تعریف شده باشند
    if (method_exists($sender, 'isBlockedBy')) {
        if ($sender->isBlockedBy($receiver->id) || $sender->hasBlocked($receiver->id)) {
            return response()->json([
                'error' => 'BLOCKED'
            ], 403);
        }
    }

    // 3. محاسبه تعداد پیام‌های رد و بدل شده (دوطرفه)
    $messagesCount = Message::where(function ($q) use ($sender, $receiver) {
            $q->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })
        ->orWhere(function ($q) use ($sender, $receiver) {
            $q->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })
        ->count();

    /**
     * 4. قانون پیام اول (رایگان اما مخفی برای گیرنده تا زمان پریمیوم شدن)
     */
    if ($messagesCount === 0) {
        $message = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'message'     => $request->message,
            'status'      => 'private', 
            'is_read'     => false,
        ]);

        return response()->json([
            'success' => true,
            'type'    => 'FIRST_MESSAGE_PRIVATE'
        ]);
    }

    /**
     * 5. بررسی محدودیت پریمیوم برای پیام‌های بعدی
     */
    if (! $sender->isPremium() && ! $receiver->isPremium()) {
        return response()->json([
            'error' => 'PREMIUM_REQUIRED',
            'receiver_id' => $receiver->id // مقدار درست جایگزین شد
        ], 402);
    }

    /**
     * 6. ارسال پیام عادی (اگر یکی پریمیوم باشد)
     */
    Message::create([
        'sender_id'   => $sender->id,
        'receiver_id' => $receiver->id,
        'message'     => $request->message,
        'status'      => 'sent',
        'is_read'     => false,
    ]);

    return response()->json([
        'success' => true
    ]);
}    

}
