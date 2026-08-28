<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_password_via_web(): void
    {
        $user = User::factory()->create([
            'password' => 'currentsecret123',
        ]);

        $response = $this->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'currentsecret123',
                'password' => 'newsecret456',
                'password_confirmation' => 'newsecret456',
            ]);

        $response->assertSessionHas('password_status');

        $user->refresh();
        $this->assertTrue(Hash::check('newsecret456', $user->password));
    }

    public function test_user_can_update_password_via_json_api(): void
    {
        $user = User::factory()->create([
            'password' => 'currentsecret123',
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'currentsecret123',
                'password' => 'newsecret456',
                'password_confirmation' => 'newsecret456',
            ]);

        $response->assertNoContent();

        $user->refresh();
        $this->assertTrue(Hash::check('newsecret456', $user->password));
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'correctpass123',
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'wrongpass123',
                'password' => 'newsecret456',
                'password_confirmation' => 'newsecret456',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);

        $user->refresh();
        $this->assertTrue(Hash::check('correctpass123', $user->password));
    }

    public function test_user_cannot_update_password_to_same_as_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'samesame123',
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'samesame123',
                'password' => 'samesame123',
                'password_confirmation' => 'samesame123',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_cannot_update_password_with_unconfirmed_new_password(): void
    {
        $user = User::factory()->create([
            'password' => 'currentsecret123',
        ]);

        $response = $this->actingAs($user)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'currentsecret123',
                'password' => 'newsecret456',
                'password_confirmation' => 'mismatchsecret789',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }
}
