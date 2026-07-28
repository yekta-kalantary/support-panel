<?php

namespace App\Http\Controllers\Portal;

use App\Enums\RecordStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $baseTickets = Ticket::query()->where('customer_id', $user->id);

        return view('portal.dashboard', [
            'stats' => [
                'activeProjects' => $user->projects()->where('status', RecordStatus::ACTIVE->value)->count(),
                'tickets' => (clone $baseTickets)->count(),
                'openTickets' => (clone $baseTickets)->where('status', TicketStatus::OPEN->value)->count(),
                'inProgressTickets' => (clone $baseTickets)->where('status', TicketStatus::IN_PROGRESS->value)->count(),
                'closedTickets' => (clone $baseTickets)->where('status', TicketStatus::CLOSED->value)->count(),
            ],
            'recentTickets' => (clone $baseTickets)
                ->with('project')
                ->latest('updated_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
