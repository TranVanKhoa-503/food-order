<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $zoneId = $this->route('deliveryZone')?->id ?? $this->route('delivery_zone');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:delivery_zones,name,'.$zoneId],
            'fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
