<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Visit;
use App\Models\Report;

class AdminStateController extends Controller
{
    public function index()
    {
        return view('admin.state.state', [
            'userCount' => User::count(),
            'messageCount' => Message::count(),
            'reportCount' => Report::count(),
            // 'visitCount' => Visit::sum('count'), // اگر جدول بازدید داری
            'chartLabels' => [], // مثلاً ['شنبه', 'یک‌شنبه', ...]
            'chartData' => [], // مثلاً [3, 7, 5, ...]
        ]);
    }
}
