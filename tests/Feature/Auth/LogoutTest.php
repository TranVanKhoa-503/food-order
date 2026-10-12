<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout_via_web(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout_via_json_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/auth/logout');

        $response->assertNoContent();
        $this->assertGuest();
    }

    public function test_guest_cannot_call_logout_api(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized();
    }

    public function test_locked_user_can_logout_via_web(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)->get('/orders')->assertForbidden();
        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_locked_user_can_logout_via_json_api(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)->getJson('/api/v1/orders')->assertForbidden();
        $this->postJson('/api/v1/auth/logout')->assertNoContent();

        $this->assertGuest();
    }
}
