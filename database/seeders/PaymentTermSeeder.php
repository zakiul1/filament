<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            ['name' => 'LC at Sight', 'code' => 'LC_SIGHT', 'days_due' => 0, 'is_active' => true],
            ['name' => 'LC 30 Days', 'code' => 'LC_30D', 'days_due' => 30, 'is_active' => true],
            ['name' => 'LC 60 Days', 'code' => 'LC_60D', 'days_due' => 60, 'is_active' => true],
            ['name' => 'DP at Sight', 'code' => 'DP_SIGHT', 'days_due' => 0, 'is_active' => true],
            ['name' => 'TT Advance 100%', 'code' => 'TT_ADV', 'days_due' => 0, 'is_active' => true],
            ['name' => 'TT 30 Days', 'code' => 'TT_30D', 'days_due' => 30, 'is_active' => true],
        ];

        DB::table('payment_terms')->insert($terms);
    }
}