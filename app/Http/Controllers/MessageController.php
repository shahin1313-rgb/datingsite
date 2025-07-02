<?php

namespace App\Http\Controllers;

use id;
use App\Models\User;
use App\Models\Message;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{


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


        return view('messages.index', compact('contacts','unreadCounts'));

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
    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Create a new message
        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->input('message'),
            'is_read' => false // Default as unread

        ]);

        return redirect()->route('messages.show', $user)->with('success', 'Message sent successfully.');
    }
}
