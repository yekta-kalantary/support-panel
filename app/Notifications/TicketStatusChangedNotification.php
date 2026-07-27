<?php

namespace App\Notifications;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketStatus $oldStatus,
        public readonly TicketStatus $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تغییر وضعیت تیکت '.$this->ticket->ticket_number)
            ->greeting('سلام '.$notifiable->full_name)
            ->line('وضعیت تیکت از «'.$this->oldStatus->label().'» به «'.$this->newStatus->label().'» تغییر کرد.')
            ->line('عنوان: '.$this->ticket->subject)
            ->action('مشاهده تیکت', route('portal.tickets.show', $this->ticket));
    }
}
