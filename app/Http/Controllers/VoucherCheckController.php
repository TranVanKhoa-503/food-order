<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherCheckController extends Controller
{
    /**
     * Check if a voucher code is valid and compute the applicable discount.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $code = strtoupper(trim($validated['voucher_code']));
        $subtotal = (float) $validated['subtotal'];

        $voucher = Voucher::query()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->first();

        if (! $voucher) {
            return response()->json([
                'message' => 'Mã giảm giá không tồn tại.',
            ], 422);
        }

        $now = now();
        if (! $voucher->is_active
            || ($voucher->starts_at && $now->lt($voucher->starts_at))
            || ($voucher->ends_at && $now->gt($voucher->ends_at))
            || ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit)) {
            return response()->json([
                'message' => 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng.',
            ], 422);
        }

        if ($subtotal < (float) $voucher->min_order_value) {
            return response()->json([
                'message' => sprintf('Đơn hàng phải từ %s ₫ để áp dụng mã này.', number_format((int) $voucher->min_order_value, 0, ',', '.')),
            ], 422);
        }

        $discountAmount = $voucher->discount_type === 'percent'
            ? (int) floor($subtotal * ((float) $voucher->discount_value / 100))
            : (int) $voucher->discount_value;

        if ($voucher->max_discount_amount !== null) {
            $discountAmount = min($discountAmount, (int) $voucher->max_discount_amount);
        }

        $discountAmount = min(max($discountAmount, 0), (int) $subtotal);

        return response()->json([
            'data' => [
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'discount_amount' => $discountAmount,
                'description' => $voucher->description,
            ],
        ]);
    }
}
