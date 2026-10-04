<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderStatusService;
use App\Support\CsvSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of all orders for admin.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = Order::with(['items', 'user']);

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if (! empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }

        if (! empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate($perPage);

        if ($request->expectsJson() || $request->is('api/*')) {
            return OrderResource::collection($orders);
        }

        return view('admin.orders.index', compact('orders', 'status', 'search', 'from', 'to'));
    }

    /**
     * Export the filtered order list as a UTF-8 CSV report.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', new Enum(OrderStatus::class)],
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $status = $validated['status'] ?? null;
        $search = trim((string) ($validated['search'] ?? ''));
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        $query = Order::query()->with('items')->latest();
        if ($status) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Mã đơn',
                'Khách hàng',
                'Số điện thoại',
                'Địa chỉ giao hàng',
                'Khu vực giao hàng',
                'Món ăn',
                'Tạm tính',
                'Giảm giá',
                'Phí giao hàng',
                'Tổng tiền',
                'Thanh toán',
                'Trạng thái thanh toán',
                'Trạng thái đơn',
                'Thời gian tạo',
            ], ';');

            $query->chunk(200, function ($orders) use ($handle): void {
                foreach ($orders as $order) {
                    $items = $order->items
                        ->map(fn ($item) => sprintf('%s x%s', $item->food_name, $item->quantity))
                        ->implode(' | ');

                    $row = [
                        $order->order_code,
                        $order->customer_name,
                        $order->customer_phone,
                        $order->delivery_address,
                        $order->delivery_zone_name ?? '',
                        $items,
                        (int) $order->subtotal,
                        (int) $order->discount_amount,
                        (int) $order->shipping_fee,
                        (int) $order->total_price,
                        $order->payment_method?->value ?? '',
                        $order->payment_status?->value ?? '',
                        $order->status?->value ?? '',
                        optional($order->created_at)->format('Y-m-d H:i:s'),
                    ];

                    fputcsv($handle, CsvSanitizer::sanitizeRow($row), ';');
                }
            });

            fclose($handle);
        }, 'orders-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Display the specified order details.
     */
    public function show(Request $request, Order $order): View|OrderResource
    {
        $order->load(['items', 'user', 'statusHistories.actor', 'voucher']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return new OrderResource($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Update the status of an order through the state machine.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): OrderResource
    {
        $targetStatus = OrderStatus::from($request->validated('status'));
        $reason = $request->validated('reason');

        $updatedOrder = $orderStatusService->transition($order, $targetStatus, $reason, $request->user());

        return new OrderResource($updatedOrder);
    }
}
