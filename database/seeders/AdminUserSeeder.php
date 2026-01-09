<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Developer (super admin) - full access
        User::updateOrCreate(
            ['email' => 'developer@maraicher.test'],
            [
                'name' => 'Developer',
                'password' => Hash::make('Dev@2026!Secure'),
                'role' => 'developer',
                'email_verified_at' => now(),
            ]
        );

        // Maraicher (admin/manager) - admin panel access
        User::updateOrCreate(
            ['email' => 'admin@maraicher.test'],
            [
                'name' => 'Admin Maraicher',
                'password' => Hash::make('Admin@2026!Secure'),
                'role' => 'maraicher',
                'email_verified_at' => now(),
            ]
        );

        // Customer - no admin panel access
        User::updateOrCreate(
            ['email' => 'customer@maraicher.test'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('Customer@2026!Secure'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
    }
}
