<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipper_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipper_id')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('base_salary', 12, 0)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0.50);
            $table->unsignedInteger('completed_orders_count')->default(0);
            $table->decimal('total_shipping_fee', 12, 0)->default(0);
            $table->decimal('total_commission', 12, 0)->default(0);
            $table->decimal('total_salary', 12, 0)->default(0);
            $table->string('status', 20)->default('draft'); // draft, finalized, paid
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['shipper_id', 'year', 'month']);
            $table->index(['year', 'month', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_payrolls');
    }
};
