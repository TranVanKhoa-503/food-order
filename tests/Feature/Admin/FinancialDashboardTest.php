<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\OperatingExpense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShipperPayroll;
use App\Models\User;
use App\Services\ShipperPayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_payroll_generation_does_not_remove_other_shippers_costs(): void
    {
        $admin = User::factory()->admin()->create();
        $shippers = User::factory()->shipper()->count(2)->create();
        foreach ($shippers as $shipper) {
            Order::factory()->create([
                'shipper_id' => $shipper->id, 'status' => OrderStatus::Completed,
                'shipping_fee' => 30000, 'completed_at' => now(),
            ]);
        }
        $url = '/api/v1/admin/dashboard?from='.now()->startOfMonth()->toDateString().'&to='.now()->endOfMonth()->toDateString();
        $this->actingAs($admin)->getJson($url)->assertOk()
            ->assertJsonPath('data.financial_summary.total_payroll_cost', 4030000);

        app(ShipperPayrollService::class)->generateOrUpdatePayroll($shippers[0], now()->year, now()->month);
        $this->getJson($url)->assertOk()
            ->assertJsonPath('data.financial_summary.total_payroll_cost', 4030000);
    }

    public function test_revenue_and_top_foods_use_completion_date_instead_of_order_creation_date(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Completed, 'total_price' => 100000,
            'created_at' => '2026-09-30 23:00:00', 'completed_at' => '2026-10-01 00:10:00',
        ]);
        OrderItem::factory()->for($order)->create(['food_name' => 'Món giao qua tháng', 'quantity' => 1, 'line_total' => 100000]);
        $this->actingAs($admin)->getJson('/api/v1/admin/dashboard?from=2026-10-01&to=2026-10-31')
            ->assertOk()->assertJsonPath('data.total_revenue', 100000)
            ->assertJsonPath('data.total_orders', 0)
            ->assertJsonPath('data.daily_revenue.2026-10-01', 100000)
            ->assertJsonPath('data.top_foods.0.food_name', 'Món giao qua tháng');
        $this->assertSame(100000, app(ShipperPayrollService::class)->getFinancialSummary('2026-10-01', '2026-10-31')['total_revenue']);
    }

    public function test_missing_payroll_estimates_are_added_per_month_without_double_counting(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 12));
        $admin = User::factory()->admin()->create();
        $shippers = User::factory()->shipper()->count(2)->create(['created_at' => '2026-09-01 00:00:00']);
        $service = app(ShipperPayrollService::class);
        foreach ([9, 10] as $month) {
            foreach ($shippers as $shipper) {
                Order::factory()->create([
                    'shipper_id' => $shipper->id, 'status' => OrderStatus::Completed,
                    'shipping_fee' => 30000, 'completed_at' => "2026-{$month}-05 12:00:00",
                ]);
            }
            $service->generateOrUpdatePayroll($shippers[0], 2026, $month);
        }
        $this->actingAs($admin)->getJson('/api/v1/admin/dashboard?from=2026-09-01&to=2026-10-31')
            ->assertOk()->assertJsonPath('data.financial_summary.total_payroll_cost', 8060000);
    }

    public function test_dashboard_calculates_and_displays_full_financial_breakdown(): void
    {
        $admin = User::factory()->admin()->create();
        $shipper = User::factory()->shipper()->create();

        // Completed order with 200,000 VND total price, 20,000 VND shipping fee
        Order::factory()->create([
            'shipper_id' => $shipper->id,
            'status' => OrderStatus::Completed,
            'total_price' => 200000,
            'shipping_fee' => 20000,
            'completed_at' => now(),
            'created_at' => now(),
        ]);

        // Operating expense of 50,000 VND
        OperatingExpense::factory()->create([
            'category' => 'utilities',
            'amount' => 50000,
            'expense_date' => now()->toDateString(),
        ]);

        // Finalized payroll: base 2,000,000 + commission 10,000 = 2,010,000 VND
        ShipperPayroll::factory()->create([
            'shipper_id' => $shipper->id,
            'year' => now()->year,
            'month' => now()->month,
            'base_salary' => 2000000,
            'total_commission' => 10000,
            'total_salary' => 2010000,
            'status' => 'finalized',
        ]);

        // API Test
        $apiResponse = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard');
        $apiResponse->assertOk()
            ->assertJsonPath('data.financial_summary.total_revenue', 200000)
            ->assertJsonPath('data.financial_summary.total_base_salary', 2000000)
            ->assertJsonPath('data.financial_summary.total_commission', 10000)
            ->assertJsonPath('data.financial_summary.total_payroll_cost', 2010000)
            ->assertJsonPath('data.financial_summary.total_operating_expenses', 50000)
            ->assertJsonPath('data.financial_summary.total_expenses', 2060000)
            ->assertJsonPath('data.financial_summary.net_profit', -1860000);

        // Web view Test
        $webResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $webResponse->assertOk()
            ->assertSee('200.000')   // Doanh thu
            ->assertSee('2.000.000') // Lương cứng
            ->assertSee('10.000')    // Hoa hồng
            ->assertSee('50.000')    // Chi phí vận hành
            ->assertSee('2.060.000') // Tổng chi phí
            ->assertSee('-1.860.000'); // Lợi nhuận âm
    }
}
