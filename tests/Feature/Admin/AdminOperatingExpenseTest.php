<?php

namespace Tests\Feature\Admin;

use App\Models\OperatingExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminOperatingExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeating_the_same_expense_submission_does_not_duplicate_costs(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = [
            'category' => 'utilities', 'amount' => 500000,
            'expense_date' => now()->toDateString(),
            'submission_token' => (string) Str::uuid(),
        ];

        $first = $this->actingAs($admin)->postJson('/api/v1/admin/expenses', $payload);
        $first->assertCreated();
        $this->postJson('/api/v1/admin/expenses', $payload)->assertOk()
            ->assertJsonPath('data.id', $first->json('data.id'));
        $this->assertDatabaseCount('operating_expenses', 1);
        $this->assertEquals(500000, OperatingExpense::sum('amount'));

        // A new submission may legitimately contain the same amount and date.
        $payload['submission_token'] = (string) Str::uuid();
        $this->postJson('/api/v1/admin/expenses', $payload)->assertCreated();
        $this->assertDatabaseCount('operating_expenses', 2);
    }

    public function test_web_expense_form_uses_a_submission_token(): void
    {
        $admin = User::factory()->admin()->create();
        $page = $this->actingAs($admin)->get('/admin/expenses')->assertOk();
        preg_match('/name="submission_token" value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertTrue(Str::isUuid($matches[1] ?? ''));
        $payload = [
            'submission_token' => $matches[1], 'category' => 'utilities',
            'amount' => 500000, 'expense_date' => now()->toDateString(),
        ];
        $this->post('/admin/expenses', $payload)->assertRedirect('/admin/expenses');
        $this->post('/admin/expenses', $payload)->assertRedirect('/admin/expenses');
        $this->assertDatabaseCount('operating_expenses', 1);
    }

    public function test_admin_can_view_and_create_operating_expense(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/expenses', [
            'category' => 'rent',
            'amount' => 15000000,
            'expense_date' => now()->toDateString(),
            'description' => 'Tiền thuê mặt bằng tháng này',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.category', 'rent')
            ->assertJsonPath('data.amount', '15000000');

        $this->assertDatabaseHas('operating_expenses', [
            'category' => 'rent',
            'amount' => 15000000,
            'created_by' => $admin->id,
        ]);

        $viewResponse = $this->actingAs($admin)->get('/admin/expenses');
        $viewResponse->assertOk()
            ->assertSee('Tiền thuê mặt bằng tháng này')
            ->assertSee('15.000.000');
    }

    public function test_operating_expense_validates_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/expenses', [
            'category' => 'invalid_category_xyz',
            'amount' => 500000,
            'expense_date' => now()->toDateString(),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }

    public function test_admin_can_delete_operating_expense(): void
    {
        $admin = User::factory()->admin()->create();
        $expense = OperatingExpense::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/v1/admin/expenses/{$expense->id}");
        $response->assertOk();

        $this->assertModelMissing($expense);
    }

    public function test_non_admin_cannot_manage_expenses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/expenses')
            ->assertForbidden();

        $this->actingAs($user)->postJson('/api/v1/admin/expenses', [
            'category' => 'utilities',
            'amount' => 1000000,
            'expense_date' => now()->toDateString(),
        ])->assertForbidden();
    }
}
