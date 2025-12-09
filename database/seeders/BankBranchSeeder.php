<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankBranchSeeder extends Seeder
{
    public function run(): void
    {
        // Assuming Bangladesh already exists in countries table
        $bangladeshId = DB::table('countries')->where('code', 'BGD')->value('id');

        $branches = [];

        $hsbcId = DB::table('banks')->where('short_name', 'HSBC')->value('id');
        if ($hsbcId) {
            $branches[] = [
                'bank_id' => $hsbcId,
                'name' => 'Gulshan Branch',
                'branch_code' => null,
                'swift_code' => 'HSBCCATT',
                'city' => 'Dhaka',
                'country_id' => $bangladeshId,
                'address' => 'Gulshan, Dhaka, Bangladesh',
                'is_active' => true,
            ];
        }

        $scbId = DB::table('banks')->where('short_name', 'SCB')->value('id');
        if ($scbId) {
            $branches[] = [
                'bank_id' => $scbId,
                'name' => 'Motijheel Branch',
                'branch_code' => null,
                'swift_code' => 'SCBLBDDX',
                'city' => 'Dhaka',
                'country_id' => $bangladeshId,
                'address' => 'Motijheel, Dhaka, Bangladesh',
                'is_active' => true,
            ];
        }

        $cityId = DB::table('banks')->where('short_name', 'CITY')->value('id');
        if ($cityId) {
            $branches[] = [
                'bank_id' => $cityId,
                'name' => 'Dhanmondi Branch',
                'branch_code' => null,
                'swift_code' => 'CIBLBDDH',
                'city' => 'Dhaka',
                'country_id' => $bangladeshId,
                'address' => 'Dhanmondi, Dhaka, Bangladesh',
                'is_active' => true,
            ];
        }

        if (!empty($branches)) {
            DB::table('bank_branches')->insert($branches);
        }
    }
}