<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->is_active;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.food_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập tên người nhận hàng.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'delivery_address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            'items.required' => 'Giỏ hàng của bạn đang trống.',
            'items.min' => 'Vui lòng chọn ít nhất một món ăn.',
            'items.*.food_id.required' => 'Mã món ăn không hợp lệ.',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng.',
            'items.*.quantity.min' => 'Số lượng tối thiểu cho mỗi món là 1.',
            'items.*.quantity.max' => 'Số lượng tối đa cho mỗi món là 99.',
        ];
    }
}
