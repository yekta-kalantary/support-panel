<?php

namespace Tests\Feature;

use App\Enums\RecordStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_customer_can_login(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertAuthenticatedAs($customer);
        $this->assertSame($customer->auth_version, session('auth_version'));
    }

    public function test_inactive_customer_cannot_login(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_active_middleware_invalidates_stale_session(): void
    {
        $customer = User::factory()->create();

        $this->signInAs($customer);

        $customer->update([
            'status' => RecordStatus::INACTIVE,
            'auth_version' => $customer->auth_version + 1,
        ]);

        $response = $this->get(route('portal.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
