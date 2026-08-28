<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a development/demo administrator.
     */
    public function run(): void
    {
        $email = (string) env('DEMO_ADMIN_EMAIL', 'admin@foodorder.test');
        $configuredPassword = env('DEMO_ADMIN_PASSWORD');
        $admin = User::query()->firstOrNew(['email' => $email]);

        $attributes = [
            'name' => 'Admin Food Order',
            'phone' => null,
            'address' => null,
            'role' => UserRole::Admin,
            'is_active' => true,
        ];

        if (! $admin->exists || filled($configuredPassword)) {
            $attributes['password'] = filled($configuredPassword)
                ? (string) $configuredPassword
                : Str::random(64);
        }

        $admin->forceFill($attributes)->save();

        if (! $admin->wasRecentlyCreated && blank($configuredPassword)) {
            return;
        }

        if (blank($configuredPassword)) {
            $this->command?->warn(
                'DEMO_ADMIN_PASSWORD is empty. The demo admin received a random password that is not displayed.',
            );
        }
    }
}
