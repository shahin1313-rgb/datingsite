<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ProfileView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        /*
         * Reuse the same membership and two-way block policy used by public
         * profile discovery. Apply it before limits and counts so hidden
         * accounts cannot affect either dashboard identities or statistics.
         */
        $discoverableByUser = fn ($query) =>
            $query->discoverableBy($user);

        $visibleProfileViews = ProfileView::query()
            ->where('viewed_id', $userId)
            ->whereHas('viewer', $discoverableByUser);

        $totalViews = (clone $visibleProfileViews)->count();

        $todayViews = (clone $visibleProfileViews)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $latestViewers = (clone $visibleProfileViews)
            ->with(['viewer' => $discoverableByUser])
            ->latest()
            ->take(3)
            ->get();

        $recentUsers = User::query()
            ->discoverableBy($user)
            ->online()
            ->latest('last_seen_at')
            ->take(3)
            ->get();

        $recentProfileViews = (clone $visibleProfileViews)
            ->with(['viewer' => $discoverableByUser])
            ->latest()
            ->take(5)
            ->get();

        $visibleLikes = $user
            ->receivedLikes()
            ->whereHas('liker', $discoverableByUser);

        $likesCount = (clone $visibleLikes)->count();

        $todayLikes = (clone $visibleLikes)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $latestLikers = (clone $visibleLikes)
            ->with(['liker' => $discoverableByUser])
            ->latest()
            ->take(5)
            ->get();

        $messagesCount = Message::query()
            ->where(function ($query) use (
                $userId,
                $discoverableByUser
            ): void {
                $query
                    ->where(function ($sent) use (
                        $userId,
                        $discoverableByUser
                    ): void {
                        $sent
                            ->where('sender_id', $userId)
                            ->whereHas(
                                'receiver',
                                $discoverableByUser
                            );
                    })
                    ->orWhere(function ($received) use (
                        $userId,
                        $discoverableByUser
                    ): void {
                        $received
                            ->where('receiver_id', $userId)
                            ->whereHas(
                                'sender',
                                $discoverableByUser
                            );
                    });
            })
            ->count();

        $membershipDaysRemaining = 0;

        if ($user->isPremium()) {
            $remainingSeconds =
                $user->premium_until->getTimestamp()
                - now()->getTimestamp();

            $membershipDaysRemaining = max(
                0,
                (int) ceil($remainingSeconds / 86400)
            );
        }

        return view('dashboard', compact(
            'user',
            'recentUsers',
            'recentProfileViews',
            'totalViews',
            'todayViews',
            'latestViewers',
            'messagesCount',
            'membershipDaysRemaining',
            'likesCount',
            'todayLikes',
            'latestLikers'
        ));
    }
}
