<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // Fetch users with pagination
        return view('admin.dashboard', compact('users'));
    }
    public function makeAdmin($id)
{
    $user = User::findOrFail($id);
    $user->role = 'admin';
    $user->save();

    return redirect()->route('admin.dashboard')->with('success', 'User promoted to admin.');
}


public function toggleBan($id)
{
    $user = User::findOrFail($id);
    $user->banned = !$user->banned;
    $user->save();

    return redirect()->route('admin.dashboard')->with('success', 'User ban status updated.');
}


public function showReports()
{
    $reports = Report::latest()->get();
    return view('admin.reports', compact('reports'));
}
}
