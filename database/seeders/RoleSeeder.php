<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin Role
        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Create Admin Role
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        $admin->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'edit_clients',
            'view_freelancers',
            'edit_freelancers',
            'view_professions',
            'view_countries',
            'view_categories',
            'view_sub_categories',
            'view_tickets',
            'reply_tickets',
        ]);

        // Create Moderator Role
        $moderator = Role::create(['name' => 'staff', 'guard_name' => 'admin']);
        $moderator->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'view_freelancers',
            'view_tickets',
            'reply_tickets',
        ]);
    }
}
