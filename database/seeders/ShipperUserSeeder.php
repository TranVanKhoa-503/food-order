<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShipperUserSeeder extends Seeder
{
    /**
     * Seed a demo shipper account for delivery operations.
     */
    public function run(): void
    {
        $email = 'shipper@foodorder.test';

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Nguyễn Văn Ship (Shipper)',
                'phone' => '0901234567',
                'address' => 'Đội Giao Hàng FoodOrder Quận 1',
                'role' => UserRole::Shipper,
                'is_active' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
