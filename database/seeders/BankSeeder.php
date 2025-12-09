<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'HSBC', 'short_name' => 'HSBC', 'swift_code' => 'HSBCCATT', 'website' => 'https://www.hsbc.com', 'phone' => null, 'is_active' => true],
            ['name' => 'Standard Chartered Bank', 'short_name' => 'SCB', 'swift_code' => 'SCBLBDDX', 'website' => 'https://www.sc.com', 'phone' => null, 'is_active' => true],
            ['name' => 'Citibank', 'short_name' => 'CITI', 'swift_code' => 'CITIBDDX', 'website' => 'https://www.citi.com', 'phone' => null, 'is_active' => true],
            ['name' => 'City Bank Limited', 'short_name' => 'CITY', 'swift_code' => 'CIBLBDDH', 'website' => 'https://www.thecitybank.com', 'phone' => null, 'is_active' => true],
            ['name' => 'BRAC Bank Limited', 'short_name' => 'BRAC', 'swift_code' => 'BRAKBDDH', 'website' => 'https://www.bracbank.com', 'phone' => null, 'is_active' => true],
            ['name' => 'Dutch-Bangla Bank Limited', 'short_name' => 'DBBL', 'swift_code' => 'DBBLBDDH', 'website' => 'https://www.dutchbanglabank.com', 'phone' => null, 'is_active' => true],
            ['name' => 'Sonali Bank Limited', 'short_name' => 'SONALI', 'swift_code' => 'BSONBDDH', 'website' => null, 'phone' => null, 'is_active' => true],
        ];

        DB::table('banks')->insert($banks);
    }
}