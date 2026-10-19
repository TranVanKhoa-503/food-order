<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('foods')
            ->where('name', 'like', '%Trà Mãng Cầu%')
            ->update([
                'description' => 'Mãng cầu xiêm tươi chín mọng dầm chua ngọt tự nhiên hòa cùng trà lài ủ lạnh thơm ngát và thạch dừa giòn sần sật.',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
