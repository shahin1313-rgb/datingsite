<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminTwoFactorCode extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $code
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('کد تأیید ورود به پنل مدیریت')
            ->greeting('سلام '.$notifiable->name)
            ->line(
                'برای تکمیل ورود به پنل مدیریت، کد زیر را وارد کنید:'
            )
            ->line($this->code)
            ->line('این کد فقط ۵ دقیقه اعتبار دارد.')
            ->line(
                'اگر شما درخواست ورود نداده‌اید، رمز عبور خود را فوراً تغییر دهید.'
            );
    }
}