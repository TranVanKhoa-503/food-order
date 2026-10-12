<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShipperPayroll;
use App\Models\User;
use App\Services\ShipperPayrollService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminPayrollController extends Controller
{
    public function __construct(
        protected ShipperPayrollService $payrollService
    ) {}

    /**
     * Display listing of shipper payrolls.
     */
    public function index(Request $request): View|JsonResponse
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $status = $request->query('status');
        $shipperId = $request->query('shipper_id');

        $query = ShipperPayroll::with(['shipper', 'finalizer', 'payer'])
            ->where('year', $year)
            ->where('month', $month);

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($shipperId)) {
            $query->where('shipper_id', $shipperId);
        }

        $payrolls = $query->latest('id')->get();
        $shippers = User::query()->where('role', UserRole::Shipper)->orderBy('name')->get();

        $totalBaseSalary = (int) $payrolls->sum('base_salary');
        $totalCommission = (int) $payrolls->sum('total_commission');
        $totalSalary = (int) $payrolls->sum('total_salary');
        $totalCompletedOrders = (int) $payrolls->sum('completed_orders_count');

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'year' => $year,
                    'month' => $month,
                    'summary' => [
                        'total_base_salary' => $totalBaseSalary,
                        'total_commission' => $totalCommission,
                        'total_salary' => $totalSalary,
                        'total_completed_orders' => $totalCompletedOrders,
                        'payrolls_count' => $payrolls->count(),
                    ],
                    'payrolls' => $payrolls,
                ],
            ]);
        }

        return view('admin.payroll.index', compact(
            'payrolls',
            'shippers',
            'year',
            'month',
            'status',
            'shipperId',
            'totalBaseSalary',
            'totalCommission',
            'totalSalary',
            'totalCompletedOrders',
        ));
    }

    /**
     * Generate or recalculate payrolls for all shippers in the given month.
     */
    public function generate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'shipper_id' => ['nullable', 'exists:users,id'],
        ]);

        $year = (int) $validated['year'];
        $month = (int) $validated['month'];

        if (! empty($validated['shipper_id'])) {
            $shipper = User::query()->where('role', UserRole::Shipper)->findOrFail($validated['shipper_id']);
            $this->payrollService->generateOrUpdatePayroll($shipper, $year, $month);
            $msg = "Đã cập nhật bảng lương tháng {$month}/{$year} cho shipper {$shipper->name}.";
        } else {
            $count = $this->payrollService->generateAllForMonth($year, $month)->count();
            $msg = "Đã tính toán bảng lương tháng {$month}/{$year} cho {$count} shipper.";
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $msg]);
        }

        return redirect()->route('admin.payrolls.index', ['year' => $year, 'month' => $month])
            ->with('success', $msg);
    }

    /**
     * Finalize draft payroll.
     */
    public function finalize(Request $request, ShipperPayroll $payroll): JsonResponse|RedirectResponse
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->payrollService->finalize($payroll, $request->user(), $request->input('note'));
        $msg = "Đã chốt bảng lương cho shipper {$payroll->shipper?->name}.";

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $msg, 'data' => $payroll->fresh(['shipper', 'finalizer'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Mark finalized payroll as paid.
     */
    public function pay(Request $request, ShipperPayroll $payroll): JsonResponse|RedirectResponse
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->payrollService->markAsPaid($payroll, $request->user(), $request->input('note'));
        $msg = "Đã xác nhận thanh toán lương cho shipper {$payroll->shipper?->name}.";

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $msg, 'data' => $payroll->fresh(['shipper', 'payer'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Show detailed payroll with completed order breakdown.
     */
    public function show(Request $request, ShipperPayroll $payroll): View|JsonResponse
    {
        $payroll->load(['shipper', 'finalizer', 'payer']);

        $orders = Order::query()
            ->where('shipper_id', $payroll->shipper_id)
            ->where('status', OrderStatus::Completed)
            ->whereYear('completed_at', $payroll->year)
            ->whereMonth('completed_at', $payroll->month)
            ->when($payroll->finalized_at, fn ($q) => $q->where('completed_at', '<=', $payroll->finalized_at))
            ->latest('completed_at')
            ->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'payroll' => $payroll,
                    'orders' => $orders,
                ],
            ]);
        }

        return view('admin.payroll.show', compact('payroll', 'orders'));
    }
}
