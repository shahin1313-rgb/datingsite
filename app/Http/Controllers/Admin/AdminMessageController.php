<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::with(['sender', 'receiver'])->latest();

        if ($request->filled('sender')) {
            $query->whereHas('sender', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->sender . '%');
            });
        }

        if ($request->filled('receiver')) {
            $query->whereHas('receiver', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->receiver . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $messages = $query->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }
}
