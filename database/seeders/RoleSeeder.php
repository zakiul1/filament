<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\CompanySetting;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'SUPER_ADMIN'],
            ['label' => 'Super Administrator']
        );

        $admin = Role::firstOrCreate(
            ['name' => 'ADMIN'],
            ['label' => 'Administrator']
        );

        // Create default Super Admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@siatex.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Attach SUPER_ADMIN role if not attached
        if (!$user->roles()->where('name', 'SUPER_ADMIN')->exists()) {
            $user->roles()->attach($superAdmin->id);
        }

        // Ensure company settings row exists
        CompanySetting::firstOrCreate([], [
            'name' => 'Siatex (BD) Ltd.',
            'base_currency_code' => 'USD',
        ]);
    }
}
