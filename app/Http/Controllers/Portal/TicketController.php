<?php

namespace App\Http\Controllers\Portal;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReplyNotification;
use App\Services\ActivityLogger;
use App\Services\TicketAttachmentService;
use App\Services\TicketNumberGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->where('customer_id', $request->user()->id)
            ->with(['project', 'latestMessage.sender'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->input('search'));

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->when($request->filled('project_id'), fn (Builder $query) => $query->where('project_id', $request->integer('project_id')))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('portal.tickets.index', [
            'tickets' => $tickets,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('portal.tickets.create', [
            'projects' => $request->user()
                ->projects()
                ->where('status', RecordStatus::ACTIVE->value)
                ->orderBy('name')
                ->get(),
            'selectedProjectId' => $request->integer('project_id') ?: null,
        ]);
    }

    public function store(
        StoreTicketRequest $request,
        TicketNumberGenerator $numberGenerator,
        TicketAttachmentService $attachments,
        ActivityLogger $logger,
    ): RedirectResponse {
        $ticket = DB::transaction(function () use ($request, $numberGenerator, $attachments, $logger): Ticket {
            $ticket = Ticket::query()->create([
                'ticket_number' => $numberGenerator->next(),
                'customer_id' => $request->user()->id,
                'project_id' => $request->validated('project_id'),
                'subject' => $request->validated('subject'),
            ]);

            $message = $ticket->messages()->create([
                'sender_id' => $request->user()->id,
                'message' => $request->validated('message'),
                'created_at' => now(),
            ]);

            $attachments->store($message, $request->file('attachments', []));

            $logger->log('ticket.created', $ticket, newValues: [
                'ticket_number' => $ticket->ticket_number,
                'project_id' => $ticket->project_id,
                'subject' => $ticket->subject,
            ], request: $request);

            return $ticket;
        });

        $ticket->load(['customer', 'project']);
        User::admins()->active()->each(fn (User $admin) => $admin->notify(new TicketCreatedNotification($ticket)));

        return redirect()
            ->route('portal.tickets.show', $ticket)
            ->with('success', 'تیکت با شماره '.$ticket->ticket_number.' ثبت شد.');
    }

    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'project',
            'messages.sender',
            'messages.attachments',
        ]);

        return view('portal.tickets.show', compact('ticket'));
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
            $ticket->touch();

            $logger->log('ticket.replied', $ticket, newValues: [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
            ], request: $request);

            return $message;
        });

        $message->load('sender');
        User::admins()->active()->each(fn (User $admin) => $admin->notify(new TicketReplyNotification($ticket, $message)));

        return back()->with('success', 'پاسخ با موفقیت ارسال شد.');
    }
}
