<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'sender' => ['nullable', 'string', 'max:100'],
            'receiver' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = Message::with(['sender', 'receiver'])->latest();

        if (! empty($filters['sender'])) {
            $query->whereHas('sender', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['sender'] . '%');
            });
        }

        if (! empty($filters['receiver'])) {
            $query->whereHas('receiver', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['receiver'] . '%');
            });
        }

        if (! empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }
}
