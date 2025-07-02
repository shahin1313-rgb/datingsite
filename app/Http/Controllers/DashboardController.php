<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


        // Fetch the 10 most recently logged-in users
        $recentUsers = User::whereNotNull('last_login_at')
                           ->orderBy('last_login_at', 'desc')
                           ->take(3)
                           ->get();

        // Pass the user data to the view
        return view('dashboard', compact('user', 'recentUsers'));
    }

}
