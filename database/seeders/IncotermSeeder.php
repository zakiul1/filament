<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncotermSeeder extends Seeder
{
    public function run(): void
    {
        $incoterms = [
            ['code' => 'EXW', 'name' => 'Ex Works', 'version' => '2020', 'is_active' => true],
            ['code' => 'FCA', 'name' => 'Free Carrier', 'version' => '2020', 'is_active' => true],
            ['code' => 'FOB', 'name' => 'Free On Board', 'version' => '2020', 'is_active' => true],
            ['code' => 'CFR', 'name' => 'Cost and Freight', 'version' => '2020', 'is_active' => true],
            ['code' => 'CIF', 'name' => 'Cost, Insurance & Freight', 'version' => '2020', 'is_active' => true],
            ['code' => 'CPT', 'name' => 'Carriage Paid To', 'version' => '2020', 'is_active' => true],
            ['code' => 'CIP', 'name' => 'Carriage & Insurance Paid To', 'version' => '2020', 'is_active' => true],
            ['code' => 'DAP', 'name' => 'Delivered At Place', 'version' => '2020', 'is_active' => true],
            ['code' => 'DPU', 'name' => 'Delivered at Place Unloaded', 'version' => '2020', 'is_active' => true],
            ['code' => 'DDP', 'name' => 'Delivered Duty Paid', 'version' => '2020', 'is_active' => true],
        ];

        DB::table('incoterms')->insert($incoterms);
    }
}