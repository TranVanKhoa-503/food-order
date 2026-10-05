<?php

namespace App\Http\Requests\Shipper;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipperOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isShipper() || $this->user()?->isAdmin();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:completed,cancelled'],
            'reason' => ['required_if:status,cancelled', 'nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái giao hàng.',
            'status.in' => 'Shipper chỉ có quyền xác nhận giao thành công (completed) hoặc giao thất bại (cancelled).',
            'reason.required_if' => 'Vui lòng nhập lý do giao hàng thất bại.',
        ];
    }
}
