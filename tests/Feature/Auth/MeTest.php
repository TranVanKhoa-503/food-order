<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_auth_me(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_authenticated_active_user_can_get_their_own_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Nguyễn Văn Me',
            'email' => 'me@example.com',
            'phone' => '0901234567',
            'address' => 'Hà Nội',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'Nguyễn Văn Me')
            ->assertJsonPath('data.email', 'me@example.com')
            ->assertJsonPath('data.phone', '0901234567')
            ->assertJsonPath('data.address', 'Hà Nội')
            ->assertJsonPath('data.role', 'user')
            ->assertJsonPath('data.is_active', true);
    }

    public function test_inactive_user_cannot_access_auth_me(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/auth/me')
            ->assertForbidden();
    }
}
