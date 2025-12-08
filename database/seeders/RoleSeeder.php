<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(
            ['name' => 'SUPER_ADMIN'],
            ['label' => 'Super Administrator']
        );

        $admin = Role::firstOrCreate(
            ['name' => 'ADMIN'],
            ['label' => 'Administrator']
        );

        // Attach SUPER_ADMIN to first user (adjust logic as you like)
        $firstUser = User::first();
        if ($firstUser && !$firstUser->roles()->where('name', 'SUPER_ADMIN')->exists()) {
            $firstUser->roles()->attach($superAdmin->id);
        }

        // Ensure company settings row exists
        \App\Models\CompanySetting::firstOrCreate([], [
            'name' => 'Siatex (BD) Ltd.',
            'base_currency_code' => 'USD',
        ]);
    }
}