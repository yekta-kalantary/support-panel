<?php

namespace Tests\Feature\Portal;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_own_project_information_and_tickets(): void
    {
        $customer = User::factory()->create();
        $project = Project::factory()->for($customer, 'customer')->create([
            'name' => 'پروژه مشتری',
            'website_url' => 'https://customer.example.com',
        ]);
        $ticket = Ticket::factory()->forProject($project)->create([
            'subject' => 'درخواست بروزرسانی سایت',
        ]);

        $this->signInAs($customer)
            ->get(route('portal.projects.show', $project))
            ->assertOk()
            ->assertSee($project->name)
            ->assertSee($project->website_url)
            ->assertSee($ticket->ticket_number)
            ->assertSee($ticket->subject);
    }

    public function test_customer_cannot_view_another_customers_project(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $otherProject = Project::factory()->for($otherCustomer, 'customer')->create();

        $this->signInAs($customer)
            ->get(route('portal.projects.show', $otherProject))
            ->assertForbidden();
    }
}
