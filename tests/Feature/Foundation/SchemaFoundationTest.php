<?php

namespace Tests\Feature\Foundation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_tables_and_required_columns_are_created(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'id',
            'name',
            'email',
            'password',
            'phone',
            'address',
            'role',
            'is_active',
        ]));

        $this->assertTrue(Schema::hasColumns('categories', [
            'id',
            'name',
            'slug',
            'description',
            'icon',
        ]));

        $this->assertTrue(Schema::hasColumns('foods', [
            'id',
            'category_id',
            'name',
            'description',
            'price',
            'image',
            'is_available',
        ]));

        $this->assertTrue(Schema::hasColumns('orders', [
            'id',
            'order_code',
            'user_id',
            'customer_name',
            'customer_phone',
            'delivery_address',
            'subtotal',
            'shipping_fee',
            'total_price',
            'payment_method',
            'payment_status',
            'status',
        ]));

        $this->assertTrue(Schema::hasColumns('order_items', [
            'id',
            'order_id',
            'food_id',
            'food_name',
            'unit_price',
            'quantity',
            'line_total',
        ]));
    }
}
