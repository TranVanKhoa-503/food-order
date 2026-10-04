<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Quận 1', 'fee' => 15000],
            ['name' => 'Quận 3', 'fee' => 15000],
            ['name' => 'Bình Thạnh', 'fee' => 20000],
        ] as $zone) {
            DeliveryZone::updateOrCreate(
                ['name' => $zone['name']],
                [...$zone, 'is_active' => true],
            );
        }
    }
}
