<?php

namespace Tests\Feature\Admin;

use App\Enums\RecordStatus;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();

        $response = $this->signInAs($admin)->post(route('admin.customers.store'), [
            'first_name' => 'یکتا',
            'last_name' => 'کلانتری',
            'email' => 'yekta@example.com',
            'mobile' => '09123456789',
            'password' => 'secure123',
            'password_confirmation' => 'secure123',
            'status' => RecordStatus::ACTIVE->value,
        ]);

        $response->assertRedirect(route('admin.customers.index'));

        $customer = User::query()->where('email', 'yekta@example.com')->firstOrFail();

        $this->assertTrue($customer->isCustomer());
        $this->assertTrue($customer->isActive());
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'customer.created',
            'subject_id' => $customer->id,
        ]);

        Notification::assertSentTo($customer, AccountCreatedNotification::class);
    }

    public function test_customer_cannot_access_admin_customer_management(): void
    {
        $customer = User::factory()->create();

        $this->signInAs($customer)
            ->get(route('admin.customers.index'))
            ->assertForbidden();
    }

    public function test_deactivating_customer_increments_auth_version(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create(['auth_version' => 4]);

        $this->signInAs($admin)
            ->put(route('admin.customers.update', $customer), [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'mobile' => $customer->mobile,
                'password' => null,
                'password_confirmation' => null,
                'status' => RecordStatus::INACTIVE->value,
            ])
            ->assertSessionHasNoErrors();

        $customer->refresh();

        $this->assertSame(5, $customer->auth_version);
        $this->assertSame(RecordStatus::INACTIVE, $customer->status);
    }
}
