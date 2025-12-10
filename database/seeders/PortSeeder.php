<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Port;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        // Define ports using country_name instead of raw country_id
        $ports = [
            // 🇧🇩 BANGLADESH
            ['name' => 'Chittagong Port', 'country_name' => 'Bangladesh', 'code' => 'BDCGP', 'type' => 'Sea'],
            ['name' => 'Mongla Port', 'country_name' => 'Bangladesh', 'code' => 'BDMGL', 'type' => 'Sea'],
            ['name' => 'Pangaon ICT', 'country_name' => 'Bangladesh', 'code' => 'BDPIC', 'type' => 'Sea'],
            ['name' => 'Kamalam ICT', 'country_name' => 'Bangladesh', 'code' => 'BDKML', 'type' => 'Sea'],
            ['name' => 'Hazrat Shahjalal International Airport', 'country_name' => 'Bangladesh', 'code' => 'DAC', 'type' => 'Air'],
            ['name' => 'Osmani International Airport', 'country_name' => 'Bangladesh', 'code' => 'ZYL', 'type' => 'Air'],

            // 🌍 EUROPE
            ['name' => 'Hamburg Port', 'country_name' => 'Germany', 'code' => 'DEHAM', 'type' => 'Sea'],
            ['name' => 'Rotterdam Port', 'country_name' => 'Netherlands', 'code' => 'NLRTM', 'type' => 'Sea'],
            ['name' => 'Antwerp Port', 'country_name' => 'Belgium', 'code' => 'BEANR', 'type' => 'Sea'],
            ['name' => 'Barcelona Port', 'country_name' => 'Spain', 'code' => 'ESBCN', 'type' => 'Sea'],
            ['name' => 'Felixstowe Port', 'country_name' => 'United Kingdom', 'code' => 'GBFXT', 'type' => 'Sea'],
            ['name' => 'Southampton Port', 'country_name' => 'United Kingdom', 'code' => 'GBSOU', 'type' => 'Sea'],

            // 🇺🇸 USA
            ['name' => 'Port of New York/New Jersey', 'country_name' => 'United States', 'code' => 'USNYC', 'type' => 'Sea'],
            ['name' => 'Port of Long Beach', 'country_name' => 'United States', 'code' => 'USLGB', 'type' => 'Sea'],
            ['name' => 'Port of Los Angeles', 'country_name' => 'United States', 'code' => 'USLAX', 'type' => 'Sea'],
            ['name' => 'Port of Savannah', 'country_name' => 'United States', 'code' => 'USSAV', 'type' => 'Sea'],
            ['name' => 'JFK International Airport', 'country_name' => 'United States', 'code' => 'JFK', 'type' => 'Air'],
            ['name' => 'LAX Airport', 'country_name' => 'United States', 'code' => 'LAX', 'type' => 'Air'],

            // 🇨🇳 CHINA
            ['name' => 'Port of Shanghai', 'country_name' => 'China', 'code' => 'CNSHA', 'type' => 'Sea'],
            ['name' => 'Port of Ningbo', 'country_name' => 'China', 'code' => 'CNNGB', 'type' => 'Sea'],
            ['name' => 'Port of Shenzhen', 'country_name' => 'China', 'code' => 'CNSZX', 'type' => 'Sea'],
            ['name' => 'Shanghai Pudong Airport', 'country_name' => 'China', 'code' => 'PVG', 'type' => 'Air'],

            // 🇹🇷 TURKEY
            ['name' => 'Port of Istanbul', 'country_name' => 'Turkey', 'code' => 'TRIST', 'type' => 'Sea'],
            ['name' => 'Istanbul Airport', 'country_name' => 'Turkey', 'code' => 'IST', 'type' => 'Air'],

            // 🇮🇳 INDIA
            ['name' => 'Nhava Sheva (JNPT)', 'country_name' => 'India', 'code' => 'INNSA', 'type' => 'Sea'],
            ['name' => 'Chennai Port', 'country_name' => 'India', 'code' => 'INMAA', 'type' => 'Sea'],

            // 🇵🇰 PAKISTAN
            ['name' => 'Karachi Port', 'country_name' => 'Pakistan', 'code' => 'PKKHI', 'type' => 'Sea'],
            ['name' => 'Port Qasim', 'country_name' => 'Pakistan', 'code' => 'PKBQM', 'type' => 'Sea'],

            // 🇦🇪 UAE
            ['name' => 'Jebel Ali Port', 'country_name' => 'United Arab Emirates', 'code' => 'AEJEA', 'type' => 'Sea'],
            ['name' => 'Dubai International Airport', 'country_name' => 'United Arab Emirates', 'code' => 'DXB', 'type' => 'Air'],
        ];

        $portColumns = Schema::getColumnListing('ports');

        foreach ($ports as $p) {
            $countryName = $p['country_name'] ?? null;

            $countryId = null;
            if ($countryName) {
                // try match by name (you can adjust to ISO code if needed)
                $countryId = Country::where('name', 'like', $countryName . '%')->value('id');
            }

            // fallback: if not found, use first country (to not break FK)
            if (!$countryId) {
                $countryId = Country::first()?->id;
            }

            // Build data only with existing columns
            $data = [];

            if (in_array('name', $portColumns)) {
                $data['name'] = $p['name'];
            }

            if (in_array('code', $portColumns)) {
                $data['code'] = $p['code'];
            }

            if (in_array('country_id', $portColumns)) {
                $data['country_id'] = $countryId;
            }

            if (in_array('type', $portColumns) && isset($p['type'])) {
                $data['type'] = $p['type'];
            }

            if (in_array('is_active', $portColumns)) {
                $data['is_active'] = true;
            }

            // If code column exists, use it as unique key. Otherwise, fall back to name.
            $uniqueKey = in_array('code', $portColumns) ? ['code' => $p['code']] : ['name' => $p['name']];

            Port::updateOrCreate($uniqueKey, $data);
        }
    }
}