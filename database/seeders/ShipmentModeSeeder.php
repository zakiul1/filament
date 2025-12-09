<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentModeSeeder extends Seeder
{
    public function run(): void
    {
        $modes = [
            ['name' => 'Sea', 'code' => 'SEA', 'is_active' => true],
            ['name' => 'Air', 'code' => 'AIR', 'is_active' => true],
            ['name' => 'Courier', 'code' => 'COURIER', 'is_active' => true],
        ];

        DB::table('shipment_modes')->insert($modes);
    }
}