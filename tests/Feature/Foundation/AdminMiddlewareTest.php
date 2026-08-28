<?php

namespace Tests\Feature\Foundation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'active', 'admin'])
            ->get('/_foundation/admin', fn () => response()->noContent());
    }

    public function test_guest_cannot_access_an_admin_route(): void
    {
        $this->getJson('/_foundation/admin')
            ->assertUnauthorized();
    }

    public function test_regular_user_cannot_access_an_admin_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/_foundation/admin')
            ->assertForbidden();
    }

    public function test_inactive_admin_cannot_access_an_admin_route(): void
    {
        $admin = User::factory()->admin()->inactive()->create();

        $this->actingAs($admin)
            ->getJson('/_foundation/admin')
            ->assertForbidden();
    }

    public function test_active_admin_can_access_an_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson('/_foundation/admin')
            ->assertNoContent();
    }
}
