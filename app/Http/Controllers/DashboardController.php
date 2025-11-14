<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfileView; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {

        // Fetch a specific user by ID
        // $user = User::findOrFail($userId);
        // return view('dashboard',compact('user'));

        // return view('dashboard');

        // Fetch the authenticated user
        $user = Auth::user();
        $userId = $user->id; 


        // آمار بازدیدها
    $totalViews = ProfileView::where('viewed_id', $userId)->count();

    $todayViews = ProfileView::where('viewed_id', $userId)
        ->whereDate('created_at', Carbon::today())
        ->count();

    $latestViewers = ProfileView::where('viewed_id', $userId)
        ->latest()
        ->take(3)
        ->with('viewer')
        ->get();


        // Fetch the 10 most recently logged-in users
        $recentUsers = User::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->take(3)
            ->get();

        $recentProfileViews = Auth::user()
            ->profileViewsReceived()
            ->with('viewer')
            ->take(5)
            ->get();

        // Pass the user data to the view
        return view('dashboard', compact('user', 'recentUsers', 'recentProfileViews', 'totalViews',
        'todayViews',
        'latestViewers'));
    }
}
