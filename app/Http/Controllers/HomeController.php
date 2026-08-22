<?php

namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    /**
     * تمام متدهای این کنترلر فقط برای کاربران واردشده قابل استفاده هستند.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * نمایش فهرست کاربران در صفحه اصلی.
     */
    public function index()
    {
        $profiles = User::query()
            ->where('id', '!=', auth()->id())
            ->latest()
            ->paginate(10);

        return view('home', compact('profiles'));
    }
}