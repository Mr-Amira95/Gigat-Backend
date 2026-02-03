<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'admin']);

        $admins = [
            [
                'username' => 'Bilal',
                'email' => 'bilal.dev@gigat.com',
                'password' => bcrypt('developer123'),
                'is_active' => true,
                'avatar' => null,
            ],
            [
                'username' => 'Abood',
                'email' => 'abood@gigat.com',
                'password' => bcrypt('password123'),
                'is_active' => true,
                'avatar' => null,
            ]
        ];

        // Create and assign roles
        $bilal = Admin::create($admins[0]);
        $bilal->assignRole($superAdminRole);

        $admin = Admin::create($admins[1]);
        $admin->assignRole($adminRole);
    }
}
