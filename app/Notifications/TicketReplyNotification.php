<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketMessage $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $route = $notifiable->isAdmin()
            ? route('admin.tickets.show', $this->ticket)
            : route('portal.tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject('پاسخ جدید به تیکت '.$this->ticket->ticket_number)
            ->greeting('سلام '.$notifiable->full_name)
            ->line($this->message->sender->full_name.' پاسخ جدیدی برای تیکت ارسال کرده است.')
            ->line('عنوان: '.$this->ticket->subject)
            ->action('مشاهده پاسخ', $route);
    }
}
