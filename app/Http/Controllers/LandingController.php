<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Carbon\Carbon;


class LandingController extends Controller
{
    public function welcome()
{
    // تعداد کل پیام‌ها
    $totalMessages = \App\Models\Message::count();

    // پیام‌های این ماه
    $monthlyMessages = \App\Models\Message::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count();

    // پیام‌های امروز
    $todayMessages = \App\Models\Message::whereDate('created_at', today())->count();


    return view('welcome', compact('totalMessages', 'monthlyMessages', 'todayMessages'));
}

}
