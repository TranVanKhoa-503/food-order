<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVoucherCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_voucher_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/admin/vouchers')
            ->assertForbidden();

        $this->actingAs($user)->postJson('/api/v1/admin/vouchers', [
            'code' => 'TESTVOUCHER',
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'is_active' => true,
        ])->assertForbidden();
    }

    public function test_guest_cannot_access_voucher_management(): void
    {
        $this->getJson('/api/v1/admin/vouchers')
            ->assertUnauthorized();
    }

    protected function createVoucher(array $attributes = []): Voucher
    {
        return Voucher::create(array_merge([
            'code' => 'VOUCHER'.uniqid(),
            'description' => 'Mô tả voucher',
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'min_order_value' => 50000,
            'max_discount_amount' => null,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => true,
        ], $attributes));
    }

    public function test_admin_can_list_vouchers_with_search(): void
    {
        $admin = User::factory()->admin()->create();

        $this->createVoucher(['code' => 'SUMMER2026', 'description' => 'Ưu đãi hè']);
        $this->createVoucher(['code' => 'WINTER2026', 'description' => 'Ưu đãi đông']);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/vouchers?search=SUMMER');
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'SUMMER2026');
    }

    public function test_admin_can_create_fixed_discount_voucher(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'giam30k',
            'description' => 'Giảm 30.000 VNĐ cho đơn từ 100.000 VNĐ',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order_value' => 100000,
            'usage_limit' => 50,
            'is_active' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.code', 'GIAM30K')
            ->assertJsonPath('data.discount_type', 'fixed')
            ->assertJsonPath('data.discount_value', 30000)
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('vouchers', [
            'code' => 'GIAM30K',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order_value' => 100000,
        ]);
    }

    public function test_admin_can_create_percent_discount_voucher(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'sale20',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'max_discount_amount' => 50000,
            'is_active' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.code', 'SALE20')
            ->assertJsonPath('data.discount_value', 20);
    }

    public function test_admin_can_update_voucher(): void
    {
        $admin = User::factory()->admin()->create();
        $voucher = $this->createVoucher([
            'code' => 'OLDCODE',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->putJson('/api/v1/admin/vouchers/'.$voucher->id, [
            'code' => 'UPDATEDCODE',
            'discount_type' => 'fixed',
            'discount_value' => 25000,
            'is_active' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.code', 'UPDATEDCODE')
            ->assertJsonPath('data.discount_value', 25000)
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'code' => 'UPDATEDCODE',
            'discount_value' => 25000,
            'is_active' => false,
        ]);
    }

    public function test_duplicate_voucher_code_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $this->createVoucher(['code' => 'UNIQUECODE']);

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'UNIQUECODE',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'is_active' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code']);
    }

    public function test_voucher_validation_rules(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Percent discount > 100%
        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'INVALIDPERCENT',
            'discount_type' => 'percent',
            'discount_value' => 150,
            'is_active' => true,
        ]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['discount_value']);

        // 2. Discount value < 1
        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'ZEROVALUE',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'is_active' => true,
        ]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['discount_value']);

        // 3. ends_at before starts_at
        $response = $this->actingAs($admin)->postJson('/api/v1/admin/vouchers', [
            'code' => 'BADTIMES',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'starts_at' => now()->addDays(5)->toDateTimeString(),
            'ends_at' => now()->addDays(2)->toDateTimeString(),
            'is_active' => true,
        ]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['ends_at']);
    }

    public function test_admin_can_delete_unused_voucher(): void
    {
        $admin = User::factory()->admin()->create();
        $voucher = $this->createVoucher();

        $response = $this->actingAs($admin)->deleteJson('/api/v1/admin/vouchers/'.$voucher->id);
        $response->assertNoContent();

        $this->assertDatabaseMissing('vouchers', ['id' => $voucher->id]);
    }

    public function test_admin_cannot_delete_voucher_already_used_in_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $voucher = $this->createVoucher();
        Order::factory()->create(['voucher_id' => $voucher->id]);

        $response = $this->actingAs($admin)->deleteJson('/api/v1/admin/vouchers/'.$voucher->id);

        // 409 Conflict: must deactivate instead of delete
        $response->assertStatus(409)
            ->assertJsonPath('message', 'Voucher đã được dùng cho đơn hàng, hãy tắt trạng thái thay vì xóa.');

        $this->assertDatabaseHas('vouchers', ['id' => $voucher->id]);
    }
}
