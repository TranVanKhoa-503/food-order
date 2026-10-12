<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Models\User;
use App\Services\ShipperPayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Tests\TestCase;

class AdminPayrollManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_finalizing_payroll_recalculates_orders_completed_after_the_draft_was_created(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();
        Order::factory()->create([
            'shipper_id' => $shipper->id, 'status' => OrderStatus::Completed,
            'shipping_fee' => 30000, 'completed_at' => now(),
        ]);
        $service = app(ShipperPayrollService::class);
        $payroll = $service->generateOrUpdatePayroll($shipper, now()->year, now()->month);
        Order::factory()->create([
            'shipper_id' => $shipper->id, 'status' => OrderStatus::Completed,
            'shipping_fee' => 30000, 'completed_at' => now(),
        ]);

        $this->actingAs($admin)->postJson('/api/v1/admin/payrolls/'.$payroll->id.'/finalize')
            ->assertOk()->assertJsonPath('data.completed_orders_count', 2)
            ->assertJsonPath('data.total_commission', '30000')
            ->assertJsonPath('data.total_salary', '2030000');
        $this->getJson('/api/v1/admin/payrolls/'.$payroll->id)->assertOk()->assertJsonCount(2, 'data.orders');

        $this->postJson('/api/v1/admin/payrolls/'.$payroll->id.'/pay')->assertOk();
        $this->assertEquals(2030000, $payroll->fresh()->total_salary);
    }

    public function test_paid_payroll_cannot_be_changed_by_a_stale_finalize_request(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();
        $service = app(ShipperPayrollService::class);
        $draft = $service->generateOrUpdatePayroll($shipper, now()->year, now()->month);
        $finalized = $service->finalize($draft, $admin);
        $service->markAsPaid($finalized, $admin);

        $this->expectException(UnprocessableEntityHttpException::class);
        $service->finalize($draft, $admin);
    }

    public function test_payroll_service_calculates_correct_salary_and_commission(): void
    {
        $shipper = User::factory()->shipper()->create();

        // Create 2 completed orders for this shipper in current month
        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'shipping_fee' => 30000,
            'completed_at' => now(),
        ]);
        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'shipping_fee' => 20000,
            'completed_at' => now(),
        ]);
        // Order completed last month (should NOT be included)
        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'shipping_fee' => 40000,
            'completed_at' => now()->subMonth(),
        ]);

        $service = app(ShipperPayrollService::class);
        $result = $service->calculate($shipper, now()->year, now()->month);

        $this->assertEquals(2000000, $result['base_salary']);
        $this->assertEquals(2, $result['completed_orders_count']);
        $this->assertEquals(50000, $result['total_shipping_fee']);
        $this->assertEquals(25000, $result['total_commission']); // 50% of 50,000
        $this->assertEquals(2025000, $result['total_salary']);
    }

    public function test_admin_can_generate_and_view_payroll(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create(['name' => 'Shipper Test']);

        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'shipping_fee' => 30000,
            'completed_at' => now(),
        ]);

        $year = now()->year;
        $month = now()->month;

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/payrolls/generate', [
            'year' => $year,
            'month' => $month,
        ]);
        $response->assertOk();

        $this->assertDatabaseHas('shipper_payrolls', [
            'shipper_id' => $shipper->id,
            'year' => $year,
            'month' => $month,
            'status' => 'draft',
            'base_salary' => 2000000,
            'total_commission' => 15000,
            'total_salary' => 2015000,
        ]);

        $viewResponse = $this->actingAs($admin)->get("/admin/payrolls?year={$year}&month={$month}");
        $viewResponse->assertOk()
            ->assertSee('Shipper Test')
            ->assertSee('2.015.000');
    }

    public function test_admin_can_finalize_and_pay_payroll(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();

        $payroll = ShipperPayroll::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => 'draft',
        ]);

        // Finalize
        $finalizeResponse = $this->actingAs($admin)->postJson("/api/v1/admin/payrolls/{$payroll->id}/finalize");
        $finalizeResponse->assertOk();

        $payroll->refresh();
        $this->assertEquals('finalized', $payroll->status);
        $this->assertEquals($admin->id, $payroll->finalized_by);
        $this->assertNotNull($payroll->finalized_at);

        // Pay
        $payResponse = $this->actingAs($admin)->postJson("/api/v1/admin/payrolls/{$payroll->id}/pay");
        $payResponse->assertOk();

        $payroll->refresh();
        $this->assertEquals('paid', $payroll->status);
        $this->assertEquals($admin->id, $payroll->paid_by);
        $this->assertNotNull($payroll->paid_at);
    }
}
