<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeneficiaryCompanySeeder extends Seeder
{
    public function run(): void
    {
        $bangladeshId = DB::table('countries')->where('code', 'BGD')->value('id');
        $bdtId = DB::table('currencies')->where('code', 'BDT')->value('id');
        $usdId = DB::table('currencies')->where('code', 'USD')->value('id');

        // Fallbacks if not found
        if (!$bdtId) {
            $bdtId = $usdId;
        }

        $companies = [
            [
                'name' => 'SIATEX (BD) LTD',
                'short_name' => 'SIATEX',
                'trade_name' => 'SIATEX',
                'group_name' => 'SIATEX GROUP',

                'address_line_1' => 'House 123, Road 4',
                'address_line_2' => 'Dhanmondi',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1209',
                'country_id' => $bangladeshId,

                'phone' => '+880-2-1234567',
                'mobile' => '+880-1711-000000',
                'email' => 'info@siatexbd.com',
                'website' => 'https://www.siatexbd.com',

                'erc_no' => 'ERC-123456',
                'irc_no' => 'IRC-987654',
                'bin_no' => 'BIN-123456789',
                'vat_reg_no' => 'VAT-123456789',
                'tin_no' => 'TIN-123456789',
                'bond_license_no' => 'BOND-2025-001',

                'contact_person_name' => 'Md. Jakiul Islam',
                'contact_person_designation' => 'Managing Director',
                'contact_person_phone' => '+880-1711-000000',
                'contact_person_email' => 'md.jakiul@siatexbd.com',

                'default_currency_id' => $bdtId,
                'is_default' => true,
                'is_active' => true,
                'notes' => 'Primary exporting company of the group.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'APTEX FASHION LTD',
                'short_name' => 'APTEX',
                'trade_name' => 'APTEX FASHION',
                'group_name' => 'SIATEX GROUP',

                'address_line_1' => 'House 45, Road 11',
                'address_line_2' => 'Banani',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1213',
                'country_id' => $bangladeshId,

                'phone' => '+880-2-2222333',
                'mobile' => null,
                'email' => 'info@aptexfashion.com',
                'website' => null,

                'erc_no' => 'ERC-APTEX-001',
                'irc_no' => null,
                'bin_no' => 'BIN-APTEX-001',
                'vat_reg_no' => null,
                'tin_no' => null,
                'bond_license_no' => null,

                'contact_person_name' => 'Mr. Aptex Contact',
                'contact_person_designation' => 'Director',
                'contact_person_phone' => '+880-1711-111111',
                'contact_person_email' => 'director@aptexfashion.com',

                'default_currency_id' => $usdId ?: $bdtId,
                'is_default' => false,
                'is_active' => true,
                'notes' => 'Sister concern used for some export POs.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'DOTTA TEX LTD',
                'short_name' => 'DOTTA',
                'trade_name' => 'DOTTA TEX',
                'group_name' => 'SIATEX GROUP',

                'address_line_1' => 'Plot 10, Export Processing Zone',
                'address_line_2' => null,
                'city' => 'Chattogram',
                'state' => 'Chattogram',
                'postal_code' => '4000',
                'country_id' => $bangladeshId,

                'phone' => '+880-31-222333',
                'mobile' => null,
                'email' => 'info@dottatex.com',
                'website' => null,

                'erc_no' => 'ERC-DOTTA-001',
                'irc_no' => null,
                'bin_no' => 'BIN-DOTTA-001',
                'vat_reg_no' => null,
                'tin_no' => null,
                'bond_license_no' => 'BOND-DOTTA-001',

                'contact_person_name' => 'Mr. Dotta Contact',
                'contact_person_designation' => 'GM',
                'contact_person_phone' => '+880-1818-222333',
                'contact_person_email' => 'gm@dottatex.com',

                'default_currency_id' => $usdId ?: $bdtId,
                'is_default' => false,
                'is_active' => true,
                'notes' => 'Used for specific EU buyers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('beneficiary_companies')->insert($companies);
    }
}