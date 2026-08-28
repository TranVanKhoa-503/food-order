<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CheckoutService
{
    /**
     * Process checkout request atomically.
     *
     * @param  array{customer_name: string, customer_phone: string, delivery_address: string, note?: ?string, items: array<int, array{food_id: int, quantity: int, note?: ?string}>}  $data
     */
    public function checkout(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Gộp các item trùng food_id nếu có
            $normalizedItems = [];
            foreach ($data['items'] as $item) {
                $foodId = (int) $item['food_id'];
                $qty = (int) $item['quantity'];
                $note = trim((string) ($item['note'] ?? ''));

                if (! isset($normalizedItems[$foodId])) {
                    $normalizedItems[$foodId] = [
                        'food_id' => $foodId,
                        'quantity' => $qty,
                        'note' => $note ?: null,
                    ];
                } else {
                    $normalizedItems[$foodId]['quantity'] += $qty;
                    if ($note && ! str_contains($normalizedItems[$foodId]['note'] ?? '', $note)) {
                        $normalizedItems[$foodId]['note'] = trim(($normalizedItems[$foodId]['note'] ?? '').'; '.$note, '; ');
                    }
                }
            }

            $foodIds = array_keys($normalizedItems);

            // 2. Query lại Foods từ Database với lockForUpdate
            $foods = Food::query()
                ->whereIn('id', $foodIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 3. Kiểm tra các món ăn có tồn tại đầy đủ không
            foreach ($foodIds as $id) {
                if (! $foods->has($id)) {
                    throw ValidationException::withMessages([
                        'items' => ["Món ăn với ID #{$id} không tồn tại trong hệ thống."],
                    ]);
                }
            }

            // 4. Kiểm tra món có đang khả dụng không
            foreach ($foods as $food) {
                if (! $food->is_available) {
                    throw new ConflictHttpException("Món ăn '{$food->name}' hiện đã tạm hết hoặc ngừng phục vụ.");
                }
            }

            // 5. Tính toán tiền từ giá trong Database
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($normalizedItems as $foodId => $item) {
                /** @var Food $food */
                $food = $foods->get($foodId);
                $unitPrice = (int) $food->price;
                $quantity = min(max($item['quantity'], 1), 99);
                $lineTotal = $unitPrice * $quantity;

                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'food_id' => $food->id,
                    'food_name' => $food->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                    'note' => $item['note'],
                ];
            }

            $shippingFee = 0; // MVP Freeship
            $totalPrice = $subtotal + $shippingFee;

            // 6. Sinh mã đơn hàng duy nhất
            $orderCode = $this->generateUniqueOrderCode();

            // 7. Tạo đơn hàng (Order)
            /** @var Order $order */
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $user->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'],
                'note' => $data['note'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_price' => $totalPrice,
                'payment_method' => PaymentMethod::CashOnDelivery,
                'payment_status' => PaymentStatus::Unpaid,
                'status' => OrderStatus::Pending,
            ]);

            // 8. Tạo các OrderItem snapshot
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return $order->load(['items', 'user']);
        });
    }

    /**
     * Generate unique order code.
     */
    protected function generateUniqueOrderCode(): string
    {
        do {
            $code = 'FO-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }
}
