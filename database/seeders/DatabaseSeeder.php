<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        $this->call([
            RoleSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            ShipmentModeSeeder::class,
            IncotermSeeder::class,
            PaymentTermSeeder::class,
            BankSeeder::class,
            BankBranchSeeder::class,
            BeneficiaryCompanySeeder::class,
            BeneficiaryBankAccountSeeder::class,
            CourierSeeder::class,
            FactoryMasterSeeder::class,
        ]);
    }
}