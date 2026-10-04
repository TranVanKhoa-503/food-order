<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('FoodOrder');
            $table->boolean('is_open')->default(true);
            $table->string('opens_at', 5)->default('00:00');
            $table->string('closes_at', 5)->default('23:59');
            $table->decimal('min_order_value', 12, 0)->default(0);
            $table->decimal('shipping_fee', 12, 0)->default(0);
            $table->unsignedSmallInteger('estimated_delivery_minutes')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
