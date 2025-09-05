<?php


namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'DIRECTOR',
                'description' => 'Overall business director with full access to all systems and operations'
            ],
            [
                'name' => 'MANAGER',
                'description' => 'Manager responsible for daily operations and staff supervision'
            ],
            [
                'name' => 'SUPERVISOR',
                'description' => 'Supervisor overseeing specific departments and staff members'
            ],
            [
                'name' => 'ACCOUNTANT',
                'description' => 'Financial officer managing accounting, billing, and financial reports'
            ],
            [
                'name' => 'BAR_TENDER',
                'description' => 'Bar staff responsible for beverage service and bar operations'
            ],
            [
                'name' => 'RECEPTIONIST',
                'description' => 'Front desk staff handling guest check-ins, reservations, and customer service'
            ],
            [
                'name' => 'HOUSEKEEPER',
                'description' => 'Housekeeping staff responsible for room cleaning and maintenance'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
};