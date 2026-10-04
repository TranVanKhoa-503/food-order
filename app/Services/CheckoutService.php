<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\DeliveryZone;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CheckoutService
{
    /**
     * Process checkout request atomically.
     *
     * @param  array{customer_name: string, customer_phone: string, delivery_address: string, delivery_zone_id?: ?int, note?: ?string, voucher_code?: ?string, items: array<int, array{food_id: int, quantity: int, note?: ?string}>}  $data
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

            $storeSetting = StoreSetting::query()->first() ?? StoreSetting::create(StoreSetting::defaults());
            if (! $storeSetting->is_open) {
                throw new ConflictHttpException('Cửa hàng hiện không nhận đơn.');
            }

            $currentTime = now()->format('H:i');
            $isOvernightSchedule = $storeSetting->opens_at > $storeSetting->closes_at;
            $outsideHours = $isOvernightSchedule
                ? ($currentTime < $storeSetting->opens_at && $currentTime > $storeSetting->closes_at)
                : ($currentTime < $storeSetting->opens_at || $currentTime > $storeSetting->closes_at);
            if ($outsideHours) {
                throw new ConflictHttpException(sprintf('Cửa hàng chỉ nhận đơn từ %s đến %s.', $storeSetting->opens_at, $storeSetting->closes_at));
            }

            if ($subtotal < (int) $storeSetting->min_order_value) {
                throw ValidationException::withMessages([
                    'items' => [sprintf('Đơn hàng tối thiểu là %s ₫.', number_format((int) $storeSetting->min_order_value, 0, ',', '.'))],
                ]);
            }

            $deliveryZone = null;
            $activeZonesExist = DeliveryZone::query()->where('is_active', true)->exists();
            if ($activeZonesExist && empty($data['delivery_zone_id'])) {
                throw ValidationException::withMessages([
                    'delivery_zone_id' => ['Vui lòng chọn khu vực giao hàng.'],
                ]);
            }
            if (! empty($data['delivery_zone_id'])) {
                $deliveryZone = DeliveryZone::query()
                    ->whereKey((int) $data['delivery_zone_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();
                if (! $deliveryZone) {
                    throw ValidationException::withMessages([
                        'delivery_zone_id' => ['Khu vực giao hàng không còn phục vụ.'],
                    ]);
                }
            }

            $shippingFee = $deliveryZone ? (int) $deliveryZone->fee : (int) $storeSetting->shipping_fee;
            $discountAmount = 0;
            $voucher = null;

            if (filled($data['voucher_code'] ?? null)) {
                $voucherCode = strtoupper(trim((string) $data['voucher_code']));
                $voucher = Voucher::query()
                    ->whereRaw('UPPER(code) = ?', [$voucherCode])
                    ->lockForUpdate()
                    ->first();

                if (! $voucher) {
                    throw ValidationException::withMessages([
                        'voucher_code' => ['Mã giảm giá không tồn tại.'],
                    ]);
                }

                $now = now();
                if (! $voucher->is_active
                    || ($voucher->starts_at && $now->lt($voucher->starts_at))
                    || ($voucher->ends_at && $now->gt($voucher->ends_at))
                    || ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit)) {
                    throw ValidationException::withMessages([
                        'voucher_code' => ['Mã giảm giá đã hết hạn hoặc hết lượt sử dụng.'],
                    ]);
                }

                if ($subtotal < (int) $voucher->min_order_value) {
                    throw ValidationException::withMessages([
                        'voucher_code' => [sprintf('Đơn hàng phải từ %s ₫ để dùng mã này.', number_format((int) $voucher->min_order_value, 0, ',', '.'))],
                    ]);
                }

                $discountAmount = $voucher->discount_type === 'percent'
                    ? (int) floor($subtotal * ((float) $voucher->discount_value / 100))
                    : (int) $voucher->discount_value;

                if ($voucher->max_discount_amount !== null) {
                    $discountAmount = min($discountAmount, (int) $voucher->max_discount_amount);
                }

                $discountAmount = min(max($discountAmount, 0), $subtotal);
                $voucher->increment('used_count');
            }

            $totalPrice = max($subtotal - $discountAmount + $shippingFee, 0);

            // 6. Sinh mã đơn hàng duy nhất
            $orderCode = $this->generateUniqueOrderCode();

            // 7. Tạo đơn hàng (Order)
            /** @var Order $order */
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $user->id,
                'delivery_zone_id' => $deliveryZone?->id,
                'delivery_zone_name' => $deliveryZone?->name,
                'voucher_id' => $voucher?->id,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'],
                'note' => $data['note'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
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

            $order->statusHistories()->create([
                'actor_id' => $user->id,
                'from_status' => null,
                'to_status' => OrderStatus::Pending->value,
                'reason' => 'Khách hàng tạo đơn',
            ]);

            return $order->load(['items', 'user', 'statusHistories.actor', 'voucher']);
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
