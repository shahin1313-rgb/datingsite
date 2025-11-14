<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    view()->composer('*', function ($view) {
        if (Auth::check()) {
            $userId = Auth::id();

            // تعداد کل پیام‌های خوانده‌نشده
            $globalUnreadCount = Message::where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            $view->with('globalUnreadCount', $globalUnreadCount);
        }
    });
    }
}
