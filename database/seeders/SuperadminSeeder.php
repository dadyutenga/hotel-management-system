<?php

namespace Database\Seeders;

use App\Models\Superadmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Superadmin::create([
            'username' => 'superadmin',
            'email' => 'superadmin@system.com',
            'password_hash' => Hash::make('password123'),
            'is_active' => true,
            'last_login_at' => null,
        ]);

        Superadmin::create([
            'username' => 'admin_user',
            'email' => 'admin@system.com',
            'password_hash' => Hash::make('admin123'),
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Inactive superadmin for testing
        Superadmin::create([
            'username' => 'inactive_admin',
            'email' => 'inactive@system.com',
            'password_hash' => Hash::make('inactive123'),
            'is_active' => false,
            'last_login_at' => null,
        ]);
    }
}