<?php

namespace Tests\Feature\Admin;

use App\Enums\RecordStatus;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_multiple_projects_for_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();

        foreach (['فروشگاه اصلی', 'وب‌سایت شرکتی'] as $name) {
            $this->signInAs($admin)
                ->post(route('admin.projects.store'), [
                    'customer_id' => $customer->id,
                    'name' => $name,
                    'website_url' => 'https://example.com/'.urlencode($name),
                    'status' => RecordStatus::ACTIVE->value,
                ])
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(2, $customer->projects()->count());
    }

    public function test_admin_can_view_project_information_and_its_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create([
            'first_name' => 'یکتا',
            'last_name' => 'کلانتری',
        ]);
        $project = Project::factory()->for($customer, 'customer')->create([
            'name' => 'وب‌سایت فروشگاهی',
            'website_url' => 'https://example.com',
        ]);
        $ticket = Ticket::factory()->forProject($project)->create([
            'subject' => 'مشکل در درگاه پرداخت',
        ]);

        $this->signInAs($admin)
            ->get(route('admin.projects.show', $project))
            ->assertOk()
            ->assertSee($project->name)
            ->assertSee($project->website_url)
            ->assertSee($customer->full_name)
            ->assertSee($ticket->ticket_number)
            ->assertSee($ticket->subject);
    }

    public function test_project_with_tickets_cannot_be_transferred_to_another_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $firstCustomer = User::factory()->create();
        $secondCustomer = User::factory()->create();
        $project = Project::factory()->for($firstCustomer, 'customer')->create();
        Ticket::factory()->forProject($project)->create();

        $response = $this->signInAs($admin)
            ->from(route('admin.projects.edit', $project))
            ->put(route('admin.projects.update', $project), [
                'customer_id' => $secondCustomer->id,
                'name' => $project->name,
                'website_url' => $project->website_url,
                'status' => $project->status->value,
            ]);

        $response
            ->assertRedirect(route('admin.projects.edit', $project))
            ->assertSessionHasErrors('customer_id');

        $this->assertSame($firstCustomer->id, $project->fresh()->customer_id);
    }
}
