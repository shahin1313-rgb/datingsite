<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        $users = User::query()
            ->discoverableBy($user)
            ->get();

        $blockedUsers = Block::query()
            ->where('blocker_id', $user->id)
            ->with('blocked')
            ->get();

        return view('police.index', compact('users', 'blockedUsers'));
    }

    public function block(
        Request $request,
        int $id
    )
    {
        $user = $request->user();

        if ($user->id === $id) {
            $message = 'نمی‌توانید حساب خودتان را مسدود کنید.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        $target = User::query()
            ->publicMembers()
            ->findOrFail($id);

        DB::transaction(function () use ($user, $id): void {
            Block::firstOrCreate([
                'blocker_id' => $user->id,
                'blocked_id' => $id,
            ]);

            /*
             * بلاک، هر لایک و مچ قبلی میان دو حساب را نیز از بین می‌برد.
             */
            Like::query()
                ->where(function ($query) use ($user, $id): void {
                    $query
                        ->where('user_id', $user->id)
                        ->where('liked_user_id', $id);
                })
                ->orWhere(function ($query) use ($user, $id): void {
                    $query
                        ->where('user_id', $id)
                        ->where('liked_user_id', $user->id);
                })
                ->delete();
        });

        /*
         * The profile and chat become intentionally undiscoverable after a
         * block. Redirect to a route that remains accessible instead of
         * returning to a page that will now respond with 404.
         */
        $message = $target->name.' با موفقیت مسدود شد.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => route('messages.index'),
            ]);
        }

        return redirect()
            ->route('messages.index')
            ->with('success', $message);
    }

    public function unblock(
        Request $request,
        int $id
    )
    {
        Block::where([
            'blocker_id' => $request->user()->id,
            'blocked_id' => $id,
        ])->delete();

        return back()->with('success', 'User unblocked successfully.');
    }
}
