<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تیکت جدید: '.$this->ticket->ticket_number)
            ->greeting('سلام '.$notifiable->full_name)
            ->line('یک تیکت جدید توسط '.$this->ticket->customer->full_name.' ثبت شد.')
            ->line('پروژه: '.$this->ticket->project->name)
            ->line('عنوان: '.$this->ticket->subject)
            ->action('مشاهده تیکت', route('admin.tickets.show', $this->ticket));
    }
}
