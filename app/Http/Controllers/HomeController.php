<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $profiles = User::query()
            ->discoverableBy($request->user())
            ->latest()
            ->paginate(10);

        return view('home', compact('profiles'));
    }
}
