<?php

namespace Tests\Feature\Orders;

use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_check_percent_voucher_successfully(): void
    {
        Voucher::create([
            'code' => 'SALE50',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'max_discount_amount' => 40000,
            'min_order_value' => 50000,
            'is_active' => true,
        ]);

        // Subtotal = 100.000, 50% = 50.000 nhưng giới hạn max 40.000
        $response = $this->postJson('/api/v1/vouchers/check', [
            'voucher_code' => 'sale50',
            'subtotal' => 100000,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.code', 'SALE50')
            ->assertJsonPath('data.discount_amount', 40000);
    }

    public function test_can_check_fixed_voucher_successfully(): void
    {
        Voucher::create([
            'code' => 'GIAM20K',
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'min_order_value' => 50000,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/vouchers/check', [
            'voucher_code' => 'GIAM20K',
            'subtotal' => 80000,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.code', 'GIAM20K')
            ->assertJsonPath('data.discount_amount', 20000);
    }

    public function test_fails_when_subtotal_is_below_min_order_value(): void
    {
        Voucher::create([
            'code' => 'BIGORDER',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order_value' => 100000,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/vouchers/check', [
            'voucher_code' => 'BIGORDER',
            'subtotal' => 50000,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    public function test_fails_when_voucher_is_inactive(): void
    {
        Voucher::create([
            'code' => 'EXPIRED',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/vouchers/check', [
            'voucher_code' => 'EXPIRED',
            'subtotal' => 50000,
        ]);

        $response->assertStatus(422);
    }
}
