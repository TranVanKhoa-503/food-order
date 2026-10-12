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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipper_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('completed_at');
            $table->index(['shipper_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipper_id']);
            $table->dropIndex(['shipper_id', 'status']);
            $table->dropColumn(['shipper_id', 'assigned_at']);
        });
    }
};
