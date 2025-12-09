<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'is_default' => true, 'is_active' => true],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'is_default' => false, 'is_active' => true],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'is_default' => false, 'is_active' => true],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥', 'is_default' => false, 'is_active' => true],
            ['name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥', 'is_default' => false, 'is_active' => true],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => '$', 'is_default' => false, 'is_active' => true],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => '$', 'is_default' => false, 'is_active' => true],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHF', 'is_default' => false, 'is_active' => true],
            ['name' => 'Bangladeshi Taka', 'code' => 'BDT', 'symbol' => '৳', 'is_default' => false, 'is_active' => true],
            ['name' => 'Indian Rupee', 'code' => 'INR', 'symbol' => '₹', 'is_default' => false, 'is_active' => true],
        ];

        DB::table('currencies')->insert($currencies);
    }
}