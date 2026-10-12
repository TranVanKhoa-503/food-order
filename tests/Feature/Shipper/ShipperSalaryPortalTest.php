<?php

namespace Tests\Feature\Shipper;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipperSalaryPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_salary_portal(): void
    {
        $this->get('/shipper/salary')->assertRedirect('/login');
        $this->getJson('/api/v1/shipper/salary')->assertUnauthorized();
    }

    public function test_regular_customer_cannot_access_shipper_salary_portal(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get('/shipper/salary')->assertForbidden();
        $this->actingAs($customer)->getJson('/api/v1/shipper/salary')->assertForbidden();
    }

    public function test_shipper_can_view_personal_salary_and_commission_estimate(): void
    {
        $shipper = User::factory()->shipper()->create(['name' => 'Nguyễn Văn Giao']);

        // Create completed order for this shipper
        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'shipping_fee' => 40000,
            'completed_at' => now(),
        ]);

        // Web view
        $response = $this->actingAs($shipper)->get('/shipper/salary');
        $response->assertOk()
            ->assertSee('Ví Thu Nhập')
            ->assertSee('2.000.000') // Lương cứng
            ->assertSee('20.000');   // 50% của 40,000

        // API
        $apiResponse = $this->actingAs($shipper)->getJson('/api/v1/shipper/salary');
        $apiResponse->assertOk()
            ->assertJsonPath('data.current_month.base_salary', 2000000)
            ->assertJsonPath('data.current_month.total_commission', 20000)
            ->assertJsonPath('data.current_month.completed_orders_count', 1);
    }

    public function test_shipper_can_view_past_finalized_payrolls(): void
    {
        $shipper = User::factory()->shipper()->create();

        ShipperPayroll::factory()->create([
            'shipper_id' => $shipper->id,
            'year' => 2026,
            'month' => 9,
            'base_salary' => 2000000,
            'total_commission' => 500000,
            'total_salary' => 2500000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($shipper)->get('/shipper/salary');
        $response->assertOk()
            ->assertSee('Tháng 9/2026')
            ->assertSee('2.500.000');
    }
}
