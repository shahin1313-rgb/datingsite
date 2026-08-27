<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * ثبت سرویس‌های برنامه.
     */
    public function register(): void
    {
        /*
         * Telescope is a development-only dependency. Register the
         * application provider only when the package is installed and the
         * feature has been explicitly enabled.
         */
        if (
            (bool) config('telescope.enabled', false) &&
            class_exists(
                \Laravel\Telescope\TelescopeServiceProvider::class
            )
        ) {
            $this->app->register(
                TelescopeServiceProvider::class
            );
        }
    }

    /**
     * راه‌اندازی سرویس‌های برنامه.
     */
    public function boot(): void
    {
        $this->configureContentRateLimits();

        /*
         * شخصی‌سازی ایمیل تأیید حساب.
         *
         * لینک امضاشده و امن Laravel حفظ می‌شود؛
         * فقط عنوان و ظاهر ایمیل تغییر می‌کند.
         */
        VerifyEmail::toMailUsing(
            function (
                object $notifiable,
                string $verificationUrl
            ): MailMessage {
                return (new MailMessage)
                    ->subject(
                        'تأیید ایمیل و فعال‌سازی حساب ولورا'
                    )
                    ->view('emails.verify-email', [
                        'user' => $notifiable,

                        'verificationUrl' =>
                            $verificationUrl,

                        'expiresInMinutes' => (int) config(
                            'auth.verification.expire',
                            60
                        ),
                    ]);
            }
        );

        /*
         * نمایش تعداد پیام‌های خوانده‌نشده
         * در تمام Viewهای سایت.
         */
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $globalUnreadCount = Message::query()
                    ->where(
                        'receiver_id',
                        Auth::id()
                    )
                    ->whereNull('read_at')
                    ->count();
            } else {
                $globalUnreadCount = 0;
            }

            $view->with(
                'globalUnreadCount',
                $globalUnreadCount
            );
        });
    }

    /**
     * محدودیت نرخ عملیات‌هایی که رکورد جدید در دیتابیس می‌سازند.
     */
    private function configureContentRateLimits(): void
    {
        $userKey = static function (Request $request): string {
            $user = $request->user();

            return $user
                ? 'user:'.$user->getAuthIdentifier()
                : 'ip:'.$request->ip();
        };

        RateLimiter::for(
            'messages.store',
            static function (Request $request) use ($userKey): array {
                $key = $userKey($request);

                return [
                    Limit::perMinute(10)
                        ->by('messages:minute:'.$key),
                    Limit::perDay(200)
                        ->by('messages:day:'.$key),
                ];
            }
        );

        RateLimiter::for(
            'tickets.store',
            static function (Request $request) use ($userKey): array {
                $key = $userKey($request);

                return [
                    Limit::perHour(3)
                        ->by('tickets:hour:'.$key),
                    Limit::perDay(10)
                        ->by('tickets:day:'.$key),
                ];
            }
        );

        RateLimiter::for(
            'reports.store',
            static function (Request $request) use ($userKey): array {
                $key = $userKey($request);

                return [
                    Limit::perHour(5)
                        ->by('reports:hour:'.$key),
                    Limit::perDay(20)
                        ->by('reports:day:'.$key),
                ];
            }
        );
    }
}
