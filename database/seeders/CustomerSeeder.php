<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Pull required master IDs
        $currencyUSD = DB::table('currencies')->where('code', 'USD')->value('id');
        $incotermFOB = DB::table('incoterms')->where('code', 'FOB')->value('id');
        $paymentTT = DB::table('payment_terms')->where('code', 'TT')->value('id');
        $airMode = DB::table('shipment_modes')->where('code', 'AIR')->value('id');
        $seaMode = DB::table('shipment_modes')->where('code', 'SEA')->value('id');

        $portNY = DB::table('ports')->where('code', 'NYC')->value('id');
        $portHAM = DB::table('ports')->where('code', 'HAM')->value('id');

        // Example issuing bank branches from Phase 2.4
        $hsbcDhaka = DB::table('bank_branches')->where('name', 'Dhaka Main Branch')->value('id');
        $citiNY = DB::table('bank_branches')->where('name', 'New York Branch')->value('id');

        // ---------------------------------------------------------
        // INSERT CUSTOMERS
        // ---------------------------------------------------------
        $customers = [
            [
                'code' => 'HM01',
                'name' => 'H&M Hennes & Mauritz AB',
                'short_name' => 'H&M',
                'buyer_group' => 'H&M Group',
                'address_line_1' => 'Mäster Samuelsgatan 49',
                'address_line_2' => null,
                'city' => 'Stockholm',
                'state' => null,
                'postal_code' => '111 21',
                'country_id' => DB::table('countries')->where('code', 'SWE')->value('id'),
                'phone' => '+46-8-796-5500',
                'email' => 'info@hm.com',
                'website' => 'https://hm.com',
                'contact_person_name' => 'John Anders',
                'contact_person_designation' => 'Buying Manager',
                'contact_person_phone' => '+46-722-155500',
                'contact_person_email' => 'j.anders@hm.com',
                'vat_reg_no' => null,
                'eori_no' => null,
                'registration_no' => null,
                'default_currency_id' => $currencyUSD,
                'default_incoterm_id' => $incotermFOB,
                'default_payment_term_id' => $paymentTT,
                'default_shipment_mode_id' => $seaMode,
                'default_destination_port_id' => $portNY,
                'is_active' => true,
                'notes' => 'Top buyer for woven items.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'CA01',
                'name' => 'C&A Europe',
                'short_name' => 'C&A',
                'buyer_group' => 'C&A Global Sourcing',
                'address_line_1' => 'Wanheimer Str. 70',
                'address_line_2' => null,
                'city' => 'Düsseldorf',
                'state' => null,
                'postal_code' => '40468',
                'country_id' => DB::table('countries')->where('code', 'DEU')->value('id'),
                'phone' => '+49-211-9872-0',
                'email' => 'contact@canda.com',
                'website' => 'https://www.canda.com',
                'contact_person_name' => 'Michael Roth',
                'contact_person_designation' => 'Production Manager',
                'contact_person_phone' => '+49-151-44772233',
                'contact_person_email' => 'm.roth@canda.com',
                'vat_reg_no' => null,
                'eori_no' => null,
                'registration_no' => null,
                'default_currency_id' => $currencyUSD,
                'default_incoterm_id' => $incotermFOB,
                'default_payment_term_id' => $paymentTT,
                'default_shipment_mode_id' => $airMode,
                'default_destination_port_id' => $portHAM,
                'is_active' => true,
                'notes' => 'Strong knit buyer.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);

        // Get inserted customer IDs
        $hmId = DB::table('customers')->where('code', 'HM01')->value('id');
        $caId = DB::table('customers')->where('code', 'CA01')->value('id');

        // ---------------------------------------------------------
        // INSERT CUSTOMER BANKS
        // ---------------------------------------------------------
        $customerBanks = [
            // H&M
            [
                'customer_id' => $hmId,
                'bank_branch_id' => $hsbcDhaka,
                'label' => 'Main LC Bank',
                'is_default_lc' => true,
                'is_default_tt' => false,
                'is_active' => true,
                'notes' => 'H&M usually issues LC via HSBC.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => $hmId,
                'bank_branch_id' => $citiNY,
                'label' => 'Backup LC Bank',
                'is_default_lc' => false,
                'is_default_tt' => false,
                'is_active' => true,
                'notes' => 'Used when HSBC unavailable.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // C&A
            [
                'customer_id' => $caId,
                'bank_branch_id' => $citiNY,
                'label' => 'Primary Issuing Bank',
                'is_default_lc' => true,
                'is_default_tt' => false,
                'is_active' => true,
                'notes' => 'C&A uses Citi NY for most LCs.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customer_banks')->insert($customerBanks);
    }
}