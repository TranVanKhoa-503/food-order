<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $voucher = $this->route('voucher');
        $voucherId = is_object($voucher) ? $voucher->id : $voucher;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($voucherId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', Rule::in(['fixed', 'percent'])],
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->input('discount_type') === 'percent' && (float) $value > 100) {
                        $fail('Phần trăm giảm giá không được vượt quá 100%.');
                    }
                },
            ],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
