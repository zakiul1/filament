<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use App\Models\CustomerBank;
use App\Models\BankBranch;
use App\Models\Country;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1. Base customer dataset (with country_name instead of country_id)
        // ------------------------------------------------------------------
        $customers = [
            [
                'name' => 'H&M',
                'short_name' => 'HM',
                'address_line_1' => 'Vastra Hamngatan 10',
                'address_line_2' => null,
                'city' => 'Stockholm',
                'state' => null,
                'postal_code' => '10316',
                'country_name' => 'Sweden',
                'contact_person' => 'H&M Buying Team',
                'contact_phone' => '+46 8 796 55 00',
                'contact_email' => 'buying@hm.com',
                'email' => 'buying@hm.com',
                'phone' => '+46 8 796 55 00',
                'is_active' => true,
                'notes' => 'Major apparel buyer, fast fashion.',
            ],

            [
                'name' => 'C&A Europe',
                'short_name' => 'CA',
                'address_line_1' => 'Wiesenstrasse 45',
                'address_line_2' => null,
                'city' => 'Düsseldorf',
                'state' => null,
                'postal_code' => '40479',
                'country_name' => 'Germany',
                'contact_person' => 'C&A Purchase Dept',
                'contact_phone' => '+49 211 987 20',
                'contact_email' => 'purchase@canda.com',
                'email' => 'purchase@canda.com',
                'phone' => '+49 211 987 20',
                'is_active' => true,
                'notes' => 'European apparel buyer.',
            ],

            [
                'name' => 'Zara (Inditex)',
                'short_name' => 'ZARA',
                'address_line_1' => 'Avenida de la Diputación',
                'address_line_2' => null,
                'city' => 'Arteixo',
                'state' => null,
                'postal_code' => '15142',
                'country_name' => 'Spain',
                'contact_person' => 'Inditex Sourcing Team',
                'contact_phone' => '+34 981 18 54 00',
                'contact_email' => 'buying@inditex.com',
                'email' => 'buying@inditex.com',
                'phone' => '+34 981 18 54 00',
                'is_active' => true,
                'notes' => 'High-volume woven/knit garments.',
            ],

            [
                'name' => 'Target USA',
                'short_name' => 'TARGET',
                'address_line_1' => '1000 Nicollet Mall',
                'address_line_2' => null,
                'city' => 'Minneapolis',
                'state' => 'MN',
                'postal_code' => '55403',
                'country_name' => 'United States',
                'contact_person' => 'Target Sourcing',
                'contact_phone' => '+1 612 304 6073',
                'contact_email' => 'sourcing@target.com',
                'email' => 'sourcing@target.com',
                'phone' => '+1 612 304 6073',
                'is_active' => true,
                'notes' => 'USA-based iconic buyer.',
            ],

            [
                'name' => 'Tesco UK',
                'short_name' => 'TESCO',
                'address_line_1' => 'Shire Park, Kestrel Way',
                'address_line_2' => null,
                'city' => 'Welwyn Garden City',
                'state' => null,
                'postal_code' => 'AL71GA',
                'country_name' => 'United Kingdom',
                'contact_person' => 'Tesco Clothing Team',
                'contact_phone' => '+44 1992 632222',
                'contact_email' => 'clothing@tesco.com',
                'email' => 'clothing@tesco.com',
                'phone' => '+44 1992 632222',
                'is_active' => true,
                'notes' => 'UK hypermarket buyer.',
            ],
        ];

        $customerColumns = Schema::getColumnListing('customers');
        $customerBankColumns = Schema::getColumnListing('customer_banks');

        foreach ($customers as $payload) {
            $countryName = $payload['country_name'] ?? null;
            unset($payload['country_name']);

            // 🔍 Find country by name (adjust if your Country table has iso codes)
            $countryId = null;
            if ($countryName) {
                $countryId = Country::where('name', 'like', $countryName . '%')->value('id');
            }

            if (!$countryId) {
                $countryId = Country::first()?->id; // fallback to first country
            }

            if (in_array('country_id', $customerColumns)) {
                $payload['country_id'] = $countryId;
            }

            // Filter payload by existing customer columns
            $customerData = array_intersect_key($payload, array_flip($customerColumns));

            /** @var \App\Models\Customer $customer */
            $customer = Customer::create($customerData);

            // Attach one random bank branch if exists
            $branch = BankBranch::inRandomOrder()->first();

            if ($branch) {
                $bankPayload = [
                    'customer_id' => $customer->id,
                    'bank_branch_id' => $branch->id,
                    'account_name' => $customer->short_name . ' A/C',
                    'account_no' => 'AC-' . rand(100000, 999999),
                    'swift_code' => $branch->swift_code ?? 'UNKNOWN',
                    'is_active' => true,
                ];

                $bankData = array_intersect_key($bankPayload, array_flip($customerBankColumns));

                CustomerBank::create($bankData);
            }
        }
    }
}