<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'customers' => User::customers()->count(),
                'activeCustomers' => User::customers()->active()->count(),
                'inactiveCustomers' => User::customers()->where('status', RecordStatus::INACTIVE->value)->count(),
                'projects' => Project::query()->count(),
                'activeProjects' => Project::active()->count(),
                'openTickets' => Ticket::query()->where('status', TicketStatus::OPEN->value)->count(),
                'inProgressTickets' => Ticket::query()->where('status', TicketStatus::IN_PROGRESS->value)->count(),
                'closedTickets' => Ticket::query()->where('status', TicketStatus::CLOSED->value)->count(),
            ],
            'recentTickets' => Ticket::query()
                ->with(['customer', 'project'])
                ->latest('updated_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
