<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function show($id){
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Pass the user data to the view
        return view('profile', compact('user'));
    }

    public function showname($name){
        // Fetch the user by ID
        $user = User::findOrFail($name);

        // Pass the user data to the view
        return view('profile', compact('user'));
    }
}
