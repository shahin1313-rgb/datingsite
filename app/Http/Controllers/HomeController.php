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
        // دریافت لیست پروفایل‌ها (مثلاً ۱۰ مورد در هر صفحه)
        $profiles = User::where('id', '!=', auth()->id()) // عدم نمایش پروفایل خود کاربر
                        ->latest()
                        ->paginate(10);

        // ارسال متغیر به ویو
        return view('home', compact('profiles'));
    }

    public function show($id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);


        // Pass the user data to the view
        return view('profile', compact('user'));
    }

    public function showname($name)
    {
        // Fetch the user by ID
        $user = User::findOrFail($name);

        // Pass the user data to the view
        return view('profile', compact('user'));
    }
}
