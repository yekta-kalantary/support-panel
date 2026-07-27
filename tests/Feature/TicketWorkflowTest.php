<?php

namespace Tests\Feature;

use App\Enums\RecordStatus;
use App\Enums\TicketStatus;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketReplyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_ticket_for_own_active_project_with_attachment(): void
    {
        Notification::fake();
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $project = Project::factory()->for($customer, 'customer')->create();

        $response = $this->signInAs($customer)->post(route('portal.tickets.store'), [
            'project_id' => $project->id,
            'subject' => 'مشکل در صفحه پرداخت',
            'message' => 'صفحه پرداخت در مرورگر موبایل باز نمی‌شود.',
            'attachments' => [
                UploadedFile::fake()->create('details.pdf', 100, 'application/pdf'),
            ],
        ]);

        $ticket = Ticket::query()->firstOrFail();

        $response->assertRedirect(route('portal.tickets.show', $ticket));
        $this->assertMatchesRegularExpression('/^TKT-\d{4}-\d{6}$/', $ticket->ticket_number);
        $this->assertSame($customer->id, $ticket->customer_id);
        $this->assertSame($project->id, $ticket->project_id);
        $this->assertSame(TicketStatus::OPEN, $ticket->status);

        $attachment = TicketAttachment::query()->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);

        Notification::assertSentTo($admin, TicketCreatedNotification::class);
    }

    public function test_customer_cannot_create_ticket_for_other_customer_project(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $otherProject = Project::factory()->for($otherCustomer, 'customer')->create();

        $response = $this->signInAs($customer)
            ->from(route('portal.tickets.create'))
            ->post(route('portal.tickets.store'), [
                'project_id' => $otherProject->id,
                'subject' => 'درخواست بررسی دسترسی',
                'message' => 'این درخواست نباید برای پروژه کاربر دیگر ثبت شود.',
            ]);

        $response
            ->assertRedirect(route('portal.tickets.create'))
            ->assertSessionHasErrors('project_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_customer_cannot_create_ticket_for_inactive_project(): void
    {
        $customer = User::factory()->create();
        $project = Project::factory()
            ->for($customer, 'customer')
            ->inactive()
            ->create();

        $this->signInAs($customer)
            ->from(route('portal.tickets.create'))
            ->post(route('portal.tickets.store'), [
                'project_id' => $project->id,
                'subject' => 'درخواست برای پروژه غیرفعال',
                'message' => 'ثبت این تیکت باید توسط اعتبارسنجی رد شود.',
            ])
            ->assertSessionHasErrors('project_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_customer_cannot_view_another_customers_ticket(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $project = Project::factory()->for($owner, 'customer')->create();
        $ticket = Ticket::factory()->forProject($project)->create();

        $this->signInAs($otherCustomer)
            ->get(route('portal.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_admin_reply_moves_open_ticket_to_in_progress_and_notifies_customer(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $project = Project::factory()->for($customer, 'customer')->create();
        $ticket = Ticket::factory()->forProject($project)->create();
        TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
            'sender_id' => $customer->id,
        ]);

        $this->signInAs($admin)
            ->post(route('admin.tickets.reply', $ticket), [
                'message' => 'موضوع در حال بررسی است.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_id' => $admin->id,
            'message' => 'موضوع در حال بررسی است.',
        ]);

        Notification::assertSentTo($customer, TicketReplyNotification::class);
    }

    public function test_customer_cannot_reply_to_closed_ticket(): void
    {
        $customer = User::factory()->create();
        $project = Project::factory()->for($customer, 'customer')->create();
        $ticket = Ticket::factory()->forProject($project)->closed()->create();

        $this->signInAs($customer)
            ->post(route('portal.tickets.reply', $ticket), [
                'message' => 'پاسخ جدید مشتری',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('ticket_messages', 0);
    }

    public function test_only_ticket_owner_or_admin_can_download_attachment(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->for($owner, 'customer')->create();
        $ticket = Ticket::factory()->forProject($project)->create();
        $message = TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
            'sender_id' => $owner->id,
        ]);

        Storage::disk('local')->put('ticket-attachments/file.txt', 'private data');

        $attachment = TicketAttachment::query()->create([
            'ticket_message_id' => $message->id,
            'original_name' => 'file.txt',
            'stored_name' => 'file.txt',
            'mime_type' => 'text/plain',
            'size' => 12,
            'disk' => 'local',
            'path' => 'ticket-attachments/file.txt',
            'created_at' => now(),
        ]);

        $this->signInAs($owner)
            ->get(route('attachments.download', $attachment))
            ->assertDownload('file.txt');

        $this->signInAs($admin)
            ->get(route('attachments.download', $attachment))
            ->assertDownload('file.txt');

        $this->signInAs($otherCustomer)
            ->get(route('attachments.download', $attachment))
            ->assertForbidden();
    }
}
