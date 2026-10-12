<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operating_expenses', function (Blueprint $table) {
            $table->uuid('submission_token')->nullable();
            $table->unique(['created_by', 'submission_token'], 'expense_submission_unique');
        });
    }

    public function down(): void
    {
        Schema::table('operating_expenses', function (Blueprint $table) {
            $table->dropUnique('expense_submission_unique');
            $table->dropColumn('submission_token');
        });
    }
};
