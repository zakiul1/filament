<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class FactoryMasterSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | CLEAR EXISTING DATA (FK-SAFE ORDER)
        |--------------------------------------------------------------------------
        */
        // If you already have assignments, clear them first
        if (Schema::hasTable('factory_certificate_assignments')) {
            DB::table('factory_certificate_assignments')->delete();
        }

        DB::table('factory_subcategories')->delete();
        DB::table('factory_categories')->delete();
        DB::table('factory_certificates')->delete();

        /*
        |--------------------------------------------------------------------------
        | FACTORY CATEGORIES
        |--------------------------------------------------------------------------
        */
        $categories = [
            ['id' => 1, 'name' => 'Knit', 'slug' => 'knit', 'description' => 'Knit garments production', 'is_active' => 1],
            ['id' => 2, 'name' => 'Woven', 'slug' => 'woven', 'description' => 'Woven garments production', 'is_active' => 1],
            ['id' => 3, 'name' => 'Sweater', 'slug' => 'sweater', 'description' => 'Sweater knitting factories', 'is_active' => 1],
            ['id' => 4, 'name' => 'Denim', 'slug' => 'denim', 'description' => 'Denim garments production', 'is_active' => 1],
            ['id' => 5, 'name' => 'Printing', 'slug' => 'printing', 'description' => 'Printing service factories', 'is_active' => 1],
            ['id' => 6, 'name' => 'Embroidery', 'slug' => 'embroidery', 'description' => 'Embroidery factories', 'is_active' => 1],
        ];

        foreach ($categories as $c) {
            DB::table('factory_categories')->insert([
                'id' => $c['id'],
                'name' => $c['name'],
                'slug' => $c['slug'],
                'description' => $c['description'],
                'is_active' => $c['is_active'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FACTORY SUBCATEGORIES
        |--------------------------------------------------------------------------
        */
        $subcategories = [
            // KNIT
            ['category_id' => 1, 'name' => 'Single Jersey', 'slug' => 'single-jersey'],
            ['category_id' => 1, 'name' => 'Rib', 'slug' => 'rib'],
            ['category_id' => 1, 'name' => 'Interlock', 'slug' => 'interlock'],
            ['category_id' => 1, 'name' => 'Pique', 'slug' => 'pique'],

            // WOVEN
            ['category_id' => 2, 'name' => 'Shirts', 'slug' => 'shirts'],
            ['category_id' => 2, 'name' => 'Trousers', 'slug' => 'trousers'],
            ['category_id' => 2, 'name' => 'Jackets', 'slug' => 'jackets'],

            // SWEATER
            ['category_id' => 3, 'name' => '3GG', 'slug' => '3gg'],
            ['category_id' => 3, 'name' => '5GG', 'slug' => '5gg'],
            ['category_id' => 3, 'name' => '7GG', 'slug' => '7gg'],
            ['category_id' => 3, 'name' => '12GG', 'slug' => '12gg'],

            // DENIM
            ['category_id' => 4, 'name' => 'Denim Pants', 'slug' => 'denim-pants'],
            ['category_id' => 4, 'name' => 'Denim Jackets', 'slug' => 'denim-jackets'],
            ['category_id' => 4, 'name' => 'Washed Denim', 'slug' => 'washed-denim'],

            // PRINTING
            ['category_id' => 5, 'name' => 'Screen Print', 'slug' => 'screen-print'],
            ['category_id' => 5, 'name' => 'Digital Print', 'slug' => 'digital-print'],
            ['category_id' => 5, 'name' => 'Sublimation', 'slug' => 'sublimation'],

            // EMBROIDERY
            ['category_id' => 6, 'name' => 'Applique Embroidery', 'slug' => 'applique-embroidery'],
            ['category_id' => 6, 'name' => 'Computer Embroidery', 'slug' => 'computer-embroidery'],
        ];

        foreach ($subcategories as $sub) {
            DB::table('factory_subcategories')->insert([
                'factory_category_id' => $sub['category_id'],
                'name' => $sub['name'],
                'slug' => $sub['slug'],
                'description' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FACTORY CERTIFICATES
        |--------------------------------------------------------------------------
        */
        $certificates = [
            ['code' => 'BSCI', 'name' => 'BSCI Certification'],
            ['code' => 'SEDEX', 'name' => 'SEDEX (SMETA)'],
            ['code' => 'WRAP', 'name' => 'WRAP Certification'],
            ['code' => 'GOTS', 'name' => 'GOTS Organic Certificate'],
            ['code' => 'OEKO-TEX', 'name' => 'OEKO-TEX Standard 100'],
            ['code' => 'ISO9001', 'name' => 'ISO 9001 – Quality Management'],
            ['code' => 'ISO14001', 'name' => 'ISO 14001 – Environmental Management'],
            ['code' => 'ISO45001', 'name' => 'ISO 45001 – Occupational Health & Safety'],
        ];

        foreach ($certificates as $cert) {
            DB::table('factory_certificates')->insert([
                'code' => $cert['code'],
                'name' => $cert['name'],
                'description' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}