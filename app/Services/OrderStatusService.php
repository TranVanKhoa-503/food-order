<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderStatusService
{
    /**
     * Map of allowed transitions from current status to target statuses.
     */
    protected const TRANSITION_MAP = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['delivering'],
        'delivering' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    /**
     * Transition order to a new status atomically with lock and side effects.
     */
    public function transition(Order $order, OrderStatus $targetStatus, ?string $reason = null, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $targetStatus, $reason, $actor) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->where('id', $order->id)->lockForUpdate()->firstOrFail();

            $currentValue = $lockedOrder->status instanceof OrderStatus ? $lockedOrder->status->value : (string) $lockedOrder->status;
            $targetValue = $targetStatus->value;

            // 1. Kiểm tra nếu cùng status
            if ($currentValue === $targetValue) {
                throw new ConflictHttpException("Đơn hàng đã ở trạng thái '{$targetValue}'.");
            }

            // 2. Kiểm tra nếu đơn đã ở trạng thái kết thúc (terminal state)
            if (in_array($currentValue, ['completed', 'cancelled'], true)) {
                throw new ConflictHttpException("Không thể thay đổi trạng thái của đơn hàng đã '{$currentValue}'.");
            }

            // 3. Kiểm tra tính hợp lệ trong ma trận chuyển đổi
            $allowedTargets = self::TRANSITION_MAP[$currentValue] ?? [];
            if (! in_array($targetValue, $allowedTargets, true)) {
                throw new ConflictHttpException("Không thể chuyển trạng thái từ '{$currentValue}' sang '{$targetValue}'.");
            }

            // 4. Kiểm tra lý do hủy nếu hủy từ confirmed trở đi
            if ($targetStatus === OrderStatus::Cancelled && $currentValue !== 'pending' && blank($reason)) {
                throw new UnprocessableEntityHttpException('Vui lòng nhập lý do hủy đơn hàng.');
            }

            // 5. Áp dụng cập nhật và side effects
            $updateData = [
                'status' => $targetStatus,
            ];

            if ($targetStatus === OrderStatus::Cancelled) {
                $updateData['cancelled_at'] = now();
                $updateData['cancel_reason'] = $reason ?: 'Hủy đơn hàng';
                if ($lockedOrder->voucher_id) {
                    Voucher::query()
                        ->whereKey($lockedOrder->voucher_id)
                        ->where('used_count', '>', 0)
                        ->decrement('used_count');
                }
            } elseif ($targetStatus === OrderStatus::Completed) {
                $updateData['completed_at'] = now();
                // Với đơn COD, khi hoàn tất thì xác nhận đã thu tiền
                $updateData['payment_status'] = PaymentStatus::Paid;
            }

            $lockedOrder->update($updateData);

            $lockedOrder->statusHistories()->create([
                'actor_id' => $actor?->id,
                'from_status' => $currentValue,
                'to_status' => $targetValue,
                'reason' => $reason,
            ]);

            $lockedOrder->loadMissing('user');
            $lockedOrder->user?->notify(new OrderStatusUpdatedNotification(
                $lockedOrder,
                $currentValue,
                $targetValue,
                $reason,
            ));

            return $lockedOrder->fresh(['items', 'user', 'statusHistories.actor', 'voucher']);
        });
    }

    /**
     * User cancels their own pending order.
     */
    public function cancelByUser(Order $order, ?string $reason = null, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $reason, $actor) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()->where('id', $order->id)->lockForUpdate()->firstOrFail();

            $currentValue = $lockedOrder->status instanceof OrderStatus ? $lockedOrder->status->value : (string) $lockedOrder->status;

            if ($currentValue !== 'pending') {
                throw new ConflictHttpException("Chỉ có thể hủy đơn hàng khi đang ở trạng thái 'pending'. Trạng thái hiện tại: '{$currentValue}'.");
            }

            $lockedOrder->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason ?: 'Khách hàng hủy đơn',
            ]);

            if ($lockedOrder->voucher_id) {
                Voucher::query()
                    ->whereKey($lockedOrder->voucher_id)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }

            $lockedOrder->statusHistories()->create([
                'actor_id' => $actor?->id,
                'from_status' => $currentValue,
                'to_status' => OrderStatus::Cancelled->value,
                'reason' => $reason ?: 'Khách hàng hủy đơn',
            ]);

            $lockedOrder->loadMissing('user');
            $lockedOrder->user?->notify(new OrderStatusUpdatedNotification(
                $lockedOrder,
                $currentValue,
                OrderStatus::Cancelled->value,
                $reason ?: 'Khách hàng hủy đơn',
            ));

            return $lockedOrder->fresh(['items', 'user', 'statusHistories.actor', 'voucher']);
        });
    }
}
