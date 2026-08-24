<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * ثبت سرویس‌های برنامه.
     */
    public function register(): void
    {
        //
    }

    /**
     * راه‌اندازی سرویس‌های برنامه.
     */
    public function boot(): void
    {
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
}