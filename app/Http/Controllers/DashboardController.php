<?php

namespace App\Http\Controllers;

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

        return view('dashboard', compact(
            'user',
            'recentUsers',
            'recentProfileViews',
            'totalViews',
            'todayViews',
            'latestViewers',
            'likesCount',
            'todayLikes',
            'latestLikers'
        ));
    }
}
