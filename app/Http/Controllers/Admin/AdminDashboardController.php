<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Food;
use App\Models\OperatingExpense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShipperPayroll;
use App\Models\User;
use App\Services\ShipperPayrollService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Get aggregate statistics for admin dashboard (API) or render view (Web).
     */
    public function index(Request $request): View|JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->query('from');
        $to = $request->query('to');

        $ordersInRange = function () use ($from, $to) {
            return Order::query()
                ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
                ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));
        };

        $completedOrders = fn () => $ordersInRange()->where('status', OrderStatus::Completed);
        $totalRevenue = (int) $completedOrders()->sum('total_price');

        $totalOrders = $ordersInRange()->count();
        $todayOrders = $ordersInRange()->whereDate('created_at', today())->count();
        $pendingOrders = $ordersInRange()->where('status', OrderStatus::Pending)->count();

        // Financial & Payroll calculations
        $payrollsInRange = ShipperPayroll::query();
        if ($from && $to) {
            $startDate = Carbon::parse($from);
            $endDate = Carbon::parse($to);
            $payrollsInRange->where(function ($q) use ($startDate, $endDate) {
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
            $payrollsInRange->where(function ($q) use ($startDate) {
                $q->where('year', '>', $startDate->year)
                    ->orWhere(function ($sub) use ($startDate) {
                        $sub->where('year', $startDate->year)->where('month', '>=', $startDate->month);
                    });
            });
        } elseif ($to) {
            $endDate = Carbon::parse($to);
            $payrollsInRange->where(function ($q) use ($endDate) {
                $q->where('year', '<', $endDate->year)
                    ->orWhere(function ($sub) use ($endDate) {
                        $sub->where('year', $endDate->year)->where('month', '<=', $endDate->month);
                    });
            });
        }

        $payrollCount = $payrollsInRange->count();
        if ($payrollCount > 0) {
            $totalBaseSalary = (int) $payrollsInRange->sum('base_salary');
            $totalCommission = (int) $payrollsInRange->sum('total_commission');
        } else {
            // Dynamic estimation if no formal payroll records exist yet
            $activeShippersCount = User::query()->where('role', UserRole::Shipper)->where('is_active', true)->count();
            $totalBaseSalary = $activeShippersCount * ShipperPayrollService::DEFAULT_BASE_SALARY;
            $shippingFeeSum = (float) $completedOrders()->sum('shipping_fee');
            $totalCommission = (int) round($shippingFeeSum * ShipperPayrollService::DEFAULT_COMMISSION_RATE);
        }

        $totalPayrollCost = $totalBaseSalary + $totalCommission;

        $totalOperatingExpenses = (int) OperatingExpense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->sum('amount');

        $totalExpenses = $totalPayrollCost + $totalOperatingExpenses;
        $netProfit = $totalRevenue - $totalExpenses;

        // Aggregate status counts in a single query instead of 6 queries in a loop
        $rawStatusCounts = $ordersInRange()
            ->selectRaw('status, COUNT(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        $statusCounts = [];
        foreach (OrderStatus::cases() as $statusCase) {
            $statusCounts[$statusCase->value] = (int) ($rawStatusCounts[$statusCase->value] ?? 0);
        }

        $totalUsers = User::query()->where('role', UserRole::User)->count();
        $totalShippers = User::query()->where('role', UserRole::Shipper)->count();
        $totalFoods = Food::query()->count();
        $recentOrders = $ordersInRange()->with(['items', 'user', 'shipper'])->latest()->take(5)->get();

        $topFoods = OrderItem::query()
            ->selectRaw('food_name, SUM(quantity) as quantity, SUM(line_total) as revenue')
            ->whereHas('order', function ($query) use ($from, $to) {
                $query->where('status', OrderStatus::Completed)
                    ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
                    ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to));
            })
            ->groupBy('food_name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        $dailyStart = $from
            ? Carbon::parse($from)->startOfDay()
            : ($to ? Carbon::parse($to)->subDays(6)->startOfDay() : today()->subDays(6)->startOfDay());
        $dailyEnd = $to ? Carbon::parse($to)->endOfDay() : today()->endOfDay();

        // Aggregate daily completed revenue in a single query
        $aggregatedDaily = Order::query()
            ->where('status', OrderStatus::Completed)
            ->whereDate('created_at', '>=', $dailyStart->toDateString())
            ->whereDate('created_at', '<=', $dailyEnd->toDateString())
            ->selectRaw('DATE(created_at) as order_date, SUM(total_price) as daily_total')
            ->groupBy('order_date')
            ->pluck('daily_total', 'order_date');

        $dailyRevenue = collect();
        for ($day = $dailyStart->copy(); $day->lte($dailyEnd) && $dailyRevenue->count() < 31; $day->addDay()) {
            $date = $day->toDateString();
            $dailyRevenue->put($date, (int) ($aggregatedDaily->get($date) ?? 0));
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'today_orders' => $todayOrders,
                    'pending_orders' => $pendingOrders,
                    'orders_by_status' => $statusCounts,
                    'total_users' => $totalUsers,
                    'total_shippers' => $totalShippers,
                    'total_foods' => $totalFoods,
                    'recent_orders' => OrderResource::collection($recentOrders),
                    'top_foods' => $topFoods,
                    'daily_revenue' => $dailyRevenue,
                    'financial_summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_base_salary' => $totalBaseSalary,
                        'total_commission' => $totalCommission,
                        'total_payroll_cost' => $totalPayrollCost,
                        'total_operating_expenses' => $totalOperatingExpenses,
                        'total_expenses' => $totalExpenses,
                        'net_profit' => $netProfit,
                    ],
                    'from' => $from,
                    'to' => $to,
                ],
            ]);
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'todayOrders',
            'pendingOrders',
            'statusCounts',
            'totalUsers',
            'totalShippers',
            'totalFoods',
            'recentOrders',
            'topFoods',
            'dailyRevenue',
            'totalBaseSalary',
            'totalCommission',
            'totalPayrollCost',
            'totalOperatingExpenses',
            'totalExpenses',
            'netProfit',
            'from',
            'to',
        ));
    }
}
