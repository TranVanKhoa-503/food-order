<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user() !== null && ($this->user()->id === ($order->user_id ?? null) || $this->user()->isAdmin());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
