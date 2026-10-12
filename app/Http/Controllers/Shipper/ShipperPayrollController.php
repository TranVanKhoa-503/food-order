<?php

namespace App\Http\Controllers\Shipper;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Services\ShipperPayrollService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipperPayrollController extends Controller
{
    public function __construct(
        protected ShipperPayrollService $payrollService
    ) {}

    /**
     * Display personal payrolls and current month estimate for authenticated shipper.
     */
    public function index(Request $request): View|JsonResponse
    {
        $shipper = $request->user();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Current month estimation
        $currentEstimate = $this->payrollService->calculate($shipper, $currentYear, $currentMonth);

        // Current month completed orders
        $recentCompletedOrders = Order::query()
            ->where('shipper_id', $shipper->id)
            ->where('status', OrderStatus::Completed)
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->latest('completed_at')
            ->take(10)
            ->get();

        // History of finalized / paid / draft payroll records
        $payrollHistory = ShipperPayroll::query()
            ->where('shipper_id', $shipper->id)
            ->latest('year')
            ->latest('month')
            ->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'current_month' => [
                        'year' => $currentYear,
                        'month' => $currentMonth,
                        ...$currentEstimate,
                    ],
                    'payroll_history' => $payrollHistory,
                    'recent_completed_orders' => $recentCompletedOrders,
                ],
            ]);
        }

        return view('shipper.salary', compact(
            'currentEstimate',
            'currentYear',
            'currentMonth',
            'recentCompletedOrders',
            'payrollHistory',
        ));
    }
}
