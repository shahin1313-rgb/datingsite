<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Visit;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class AdminStateController extends Controller
{
    public function index()
    {
        // فرض: جدول بازدید‌ها دارید. اگر نه، می‌تونید به صورت دستی مقدار بدید
        $visitCount = DB::table('users')->sum('visits_count'); // اگر جدول 'visits' داریدئز

        return view('admin.state.state', [
            'userCount' => User::count(),
            'messageCount' => Message::count(),
            'reportCount' => Report::count(),
            'visitCount' => $visitCount, // اگر جدول بازدید داری
            'chartLabels' => [], // مثلاً ['شنبه', 'یک‌شنبه', ...]
            'chartData' => [], // مثلاً [3, 7, 5, ...]
        ]);
    }
}
