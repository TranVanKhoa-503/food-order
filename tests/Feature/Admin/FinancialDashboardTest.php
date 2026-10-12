<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\OperatingExpense;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase;

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
