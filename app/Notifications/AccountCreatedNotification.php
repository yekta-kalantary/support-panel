<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('حساب کاربری شما ایجاد شد')
            ->greeting('سلام '.$notifiable->full_name)
            ->line('حساب کاربری شما در پنل پشتیبانی ایجاد شده است.')
            ->line('برای دریافت یا تغییر رمز عبور می‌توانید از گزینه «فراموشی رمز عبور» استفاده کنید.')
            ->action('ورود به پنل', route('login'))
            ->line('این پیام به‌صورت خودکار ارسال شده است.');
    }
}
