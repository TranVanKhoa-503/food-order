<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\OperatingExpense;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ShipperPayrollService
{
    public const DEFAULT_BASE_SALARY = 2000000;

    public const DEFAULT_COMMISSION_RATE = 0.50;

    /**
     * Calculate monthly payroll metrics for a given shipper.
     *
     * @return array{
     *     shipper_id: int,
     *     year: int,
     *     month: int,
     *     base_salary: float,
     *     commission_rate: float,
     *     completed_orders_count: int,
     *     total_shipping_fee: float,
     *     total_commission: float,
     *     total_salary: float
     * }
     */
    public function calculate(User $shipper, int $year, int $month): array
    {
        $orders = Order::query()
            ->where('shipper_id', $shipper->id)
            ->where('status', OrderStatus::Completed)
            ->whereYear('completed_at', $year)
            ->whereMonth('completed_at', $month)
            ->get();

        $completedOrdersCount = $orders->count();
        $totalShippingFee = (float) $orders->sum('shipping_fee');
        $commissionRate = self::DEFAULT_COMMISSION_RATE;
        $totalCommission = round($totalShippingFee * $commissionRate);
        $baseSalary = (float) self::DEFAULT_BASE_SALARY;
        $totalSalary = $baseSalary + $totalCommission;

        return [
            'shipper_id' => $shipper->id,
            'year' => $year,
            'month' => $month,
            'base_salary' => $baseSalary,
            'commission_rate' => $commissionRate,
            'completed_orders_count' => $completedOrdersCount,
            'total_shipping_fee' => $totalShippingFee,
            'total_commission' => $totalCommission,
            'total_salary' => $totalSalary,
        ];
    }

    /**
     * Create or update a draft payroll for a shipper.
     */
    public function generateOrUpdatePayroll(User $shipper, int $year, int $month, ?string $note = null): ShipperPayroll
    {
        $existing = ShipperPayroll::query()
            ->where('shipper_id', $shipper->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing && $existing->status !== 'draft') {
            throw new UnprocessableEntityHttpException(
                "Bảng lương tháng {$month}/{$year} của shipper '{$shipper->name}' đã ở trạng thái '{$existing->status}' và không thể tạo lại tự động."
            );
        }

        $metrics = $this->calculate($shipper, $year, $month);

        if ($existing) {
            $existing->update([
                'base_salary' => $metrics['base_salary'],
                'commission_rate' => $metrics['commission_rate'],
                'completed_orders_count' => $metrics['completed_orders_count'],
                'total_shipping_fee' => $metrics['total_shipping_fee'],
                'total_commission' => $metrics['total_commission'],
                'total_salary' => $metrics['total_salary'],
                'note' => $note ?? $existing->note,
            ]);

            return $existing;
        }

        return ShipperPayroll::create([
            'shipper_id' => $shipper->id,
            'year' => $year,
            'month' => $month,
            'base_salary' => $metrics['base_salary'],
            'commission_rate' => $metrics['commission_rate'],
            'completed_orders_count' => $metrics['completed_orders_count'],
            'total_shipping_fee' => $metrics['total_shipping_fee'],
            'total_commission' => $metrics['total_commission'],
            'total_salary' => $metrics['total_salary'],
            'status' => 'draft',
            'note' => $note,
        ]);
    }

    /**
     * Generate or recalculate payrolls for all active shippers in a given month.
     *
     * @return Collection<int, ShipperPayroll>
     */
    public function generateAllForMonth(int $year, int $month): Collection
    {
        $shippers = User::query()
            ->where('role', UserRole::Shipper)
            ->get();

        $generated = collect();
        foreach ($shippers as $shipper) {
            $payroll = ShipperPayroll::query()
                ->where('shipper_id', $shipper->id)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            // Skip finalized or paid ones
            if ($payroll && $payroll->status !== 'draft') {
                $generated->push($payroll);
                continue;
            }

            $generated->push($this->generateOrUpdatePayroll($shipper, $year, $month));
        }

        return $generated;
    }

    /**
     * Finalize draft payroll.
     */
    public function finalize(ShipperPayroll $payroll, User $admin, ?string $note = null): ShipperPayroll
    {
        if ($payroll->status !== 'draft') {
            throw new UnprocessableEntityHttpException("Chỉ có thể chốt bảng lương đang ở trạng thái nháp (draft). Trạng thái hiện tại: '{$payroll->status}'.");
        }

        $payroll->update([
            'status' => 'finalized',
            'finalized_at' => now(),
            'finalized_by' => $admin->id,
            'note' => $note ?? $payroll->note,
        ]);

        return $payroll;
    }

    /**
     * Mark finalized payroll as paid.
     */
    public function markAsPaid(ShipperPayroll $payroll, User $admin, ?string $note = null): ShipperPayroll
    {
        if ($payroll->status !== 'finalized') {
            throw new UnprocessableEntityHttpException("Bảng lương phải được chốt (finalized) trước khi xác nhận thanh toán (paid). Trạng thái hiện tại: '{$payroll->status}'.");
        }

        $payroll->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $admin->id,
            'note' => $note ?? $payroll->note,
        ]);

        return $payroll;
    }

    /**
     * Calculate financial summary (revenue, payroll costs, operating expenses, net profit).
     *
     * @return array{
     *     total_revenue: int,
     *     total_base_salary: int,
     *     total_commission: int,
     *     total_payroll_cost: int,
     *     total_operating_expenses: int,
     *     total_cost: int,
     *     net_profit: int
     * }
     */
    public function getFinancialSummary(?string $from = null, ?string $to = null): array
    {
        // 1. Total revenue from completed orders
        $revenueQuery = Order::query()
            ->where('status', OrderStatus::Completed)
            ->when($from, fn ($q) => $q->whereDate('completed_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('completed_at', '<=', $to));

        $totalRevenue = (int) $revenueQuery->sum('total_price');

        // 2. Shipper payroll costs
        // If date range is specified, match year and month
        $payrollQuery = ShipperPayroll::query();
        if ($from && $to) {
            $startDate = Carbon::parse($from);
            $endDate = Carbon::parse($to);
            $payrollQuery->where(function ($q) use ($startDate, $endDate) {
                $cur = $startDate->copy()->startOfMonth();
                while ($cur->lte($endDate)) {
                    $q->orWhere(function ($sub) use ($cur) {
                        $sub->where('year', $cur->year)->where('month', $cur->month);
                    });
                    $cur->addMonth();
                }
            });
        } elseif ($from) {
            $startDate = Carbon::parse($from);
            $payrollQuery->where(function ($q) use ($startDate) {
                $q->where('year', '>', $startDate->year)
                    ->orWhere(function ($sub) use ($startDate) {
                        $sub->where('year', $startDate->year)->where('month', '>=', $startDate->month);
                    });
            });
        } elseif ($to) {
            $endDate = Carbon::parse($to);
            $payrollQuery->where(function ($q) use ($endDate) {
                $q->where('year', '<', $endDate->year)
                    ->orWhere(function ($sub) use ($endDate) {
                        $sub->where('year', $endDate->year)->where('month', '<=', $endDate->month);
                    });
            });
        }

        $totalBaseSalary = (int) $payrollQuery->sum('base_salary');
        $totalCommission = (int) $payrollQuery->sum('total_commission');
        $totalPayrollCost = $totalBaseSalary + $totalCommission;

        // 3. Operating expenses
        $expenseQuery = OperatingExpense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to));

        $totalOperatingExpenses = (int) $expenseQuery->sum('amount');

        // 4. Total costs & Net profit
        $totalCost = $totalPayrollCost + $totalOperatingExpenses;
        $netProfit = $totalRevenue - $totalCost;

        return [
            'total_revenue' => $totalRevenue,
            'total_base_salary' => $totalBaseSalary,
            'total_commission' => $totalCommission,
            'total_payroll_cost' => $totalPayrollCost,
            'total_operating_expenses' => $totalOperatingExpenses,
            'total_cost' => $totalCost,
            'net_profit' => $netProfit,
        ];
    }
}
