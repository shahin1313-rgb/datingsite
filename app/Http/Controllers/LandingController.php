<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingController extends Controller
{
    private const MESSAGE_STATISTICS_CACHE_KEY = 'landing.message-statistics.v1';

    private const MESSAGE_STATISTICS_CACHE_TTL_SECONDS = 300;

    public function welcome(): View
    {
        $statistics = Cache::remember(
            self::MESSAGE_STATISTICS_CACHE_KEY,
            self::MESSAGE_STATISTICS_CACHE_TTL_SECONDS,
            static function (): array {
                $now = now();

                return [
                    'totalMessages' => Message::count(),
                    'monthlyMessages' => Message::query()
                        ->whereBetween('created_at', [
                            $now->copy()->startOfMonth(),
                            $now->copy()->endOfMonth(),
                        ])
                        ->count(),
                    'todayMessages' => Message::query()
                        ->whereBetween('created_at', [
                            $now->copy()->startOfDay(),
                            $now->copy()->endOfDay(),
                        ])
                        ->count(),
                ];
            }
        );

        return view('welcome', $statistics);
    }
}
