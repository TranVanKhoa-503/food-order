<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'is_open',
        'opens_at',
        'closes_at',
        'min_order_value',
        'shipping_fee',
        'estimated_delivery_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'min_order_value' => 'decimal:0',
            'shipping_fee' => 'decimal:0',
            'estimated_delivery_minutes' => 'integer',
        ];
    }

    public static function defaults(): array
    {
        return [
            'store_name' => 'FoodOrder',
            'is_open' => true,
            'opens_at' => '00:00',
            'closes_at' => '23:59',
            'min_order_value' => 0,
            'shipping_fee' => 0,
            'estimated_delivery_minutes' => 30,
        ];
    }
}
