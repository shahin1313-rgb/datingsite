<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;

class BlockController extends Controller
{

    public function index()
    {
        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
        $blockedUsers = \App\Models\Block::where('blocker_id', auth()->id())->with('blocked')->get();

        return view('police.index', compact('users', 'blockedUsers'));
    }

    public function block(Request $request, $id)
    {
        if (auth()->user()->id == $id) {
            return back()->with('error', 'You cannot block yourself.');
        }

        Block::firstOrCreate([
            'blocker_id' => auth()->id(),
            'blocked_id' => $id,
        ]);

        return back()->with('success', 'User blocked successfully.');
    }

    public function unblock(Request $request, $id)
    {
        Block::where([
            'blocker_id' => auth()->id(),
            'blocked_id' => $id,
        ])->delete();

        return back()->with('success', 'User unblocked successfully.');
    }
}
