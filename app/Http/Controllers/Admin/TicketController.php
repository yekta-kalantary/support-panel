<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTicketStatusRequest;
use App\Http\Requests\StoreTicketReplyRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketReplyNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Services\ActivityLogger;
use App\Services\TicketAttachmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->with(['customer', 'project'])
            ->with(['latestMessage.sender'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $query) use ($search): void {
                            $query
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('project', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->when($request->filled('customer_id'), fn (Builder $query) => $query->where('customer_id', $request->integer('customer_id')))
            ->when($request->filled('project_id'), fn (Builder $query) => $query->where('project_id', $request->integer('project_id')))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'customers' => User::customers()->orderBy('first_name')->orderBy('last_name')->get(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'customer',
            'project',
            'messages.sender',
            'messages.attachments',
        ]);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(
        StoreTicketReplyRequest $request,
        Ticket $ticket,
        TicketAttachmentService $attachments,
        ActivityLogger $logger,
    ): RedirectResponse {
        $message = DB::transaction(function () use ($request, $ticket, $attachments, $logger) {
            $message = $ticket->messages()->create([
                'sender_id' => $request->user()->id,
                'message' => $request->validated('message'),
                'created_at' => now(),
            ]);

            $attachments->store($message, $request->file('attachments', []));

            if ($ticket->status === TicketStatus::OPEN) {
                $ticket->update(['status' => TicketStatus::IN_PROGRESS]);
            } else {
                $ticket->touch();
            }

            $logger->log('ticket.replied', $ticket, newValues: [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
            ], request: $request);

            return $message;
        });

        $message->load('sender');
        $ticket->customer->notify(new TicketReplyNotification($ticket, $message));

        return back()->with('success', 'پاسخ با موفقیت ارسال شد.');
    }

    public function updateStatus(
        UpdateTicketStatusRequest $request,
        Ticket $ticket,
        ActivityLogger $logger,
    ): RedirectResponse {
        $oldStatus = $ticket->status;
        $newStatus = $request->enum('status', TicketStatus::class);

        if ($oldStatus === $newStatus) {
            return back()->with('success', 'وضعیت تیکت تغییری نکرد.');
        }

        $ticket->update([
            'status' => $newStatus,
            'closed_at' => $newStatus === TicketStatus::CLOSED ? now() : null,
        ]);

        $logger->log(
            'ticket.status_changed',
            $ticket,
            oldValues: ['status' => $oldStatus->value],
            newValues: ['status' => $newStatus->value],
            request: $request,
        );

        $ticket->customer->notify(
            new TicketStatusChangedNotification($ticket, $oldStatus, $newStatus)
        );

        return back()->with('success', 'وضعیت تیکت با موفقیت تغییر کرد.');
    }
}
