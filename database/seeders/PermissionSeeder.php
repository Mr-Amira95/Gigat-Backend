<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard', 'description' => 'View Dashboard'],

            // Admins
            ['name' => 'view_admins', 'description' => 'View Administrators'],
            ['name' => 'create_admins', 'description' => 'Create Administrators'],
            ['name' => 'edit_admins', 'description' => 'Edit Administrators'],
            ['name' => 'delete_admins', 'description' => 'Delete Administrators'],

            // Clients
            ['name' => 'view_clients', 'description' => 'View Clients'],
            ['name' => 'create_clients', 'description' => 'Create Clients'],
            ['name' => 'edit_clients', 'description' => 'Edit Clients'],
            ['name' => 'delete_clients', 'description' => 'Delete Clients'],

            // Freelancers
            ['name' => 'view_freelancers', 'description' => 'View Freelancers'],
            ['name' => 'create_freelancers', 'description' => 'Create Freelancers'],
            ['name' => 'edit_freelancers', 'description' => 'Edit Freelancers'],
            ['name' => 'delete_freelancers', 'description' => 'Delete Freelancers'],

            // Professions
            ['name' => 'view_professions', 'description' => 'View Professions'],
            ['name' => 'create_professions', 'description' => 'Create Professions'],
            ['name' => 'edit_professions', 'description' => 'Edit Professions'],
            ['name' => 'delete_professions', 'description' => 'Delete Professions'],

            // Countries
            ['name' => 'view_countries', 'description' => 'View Countries'],
            ['name' => 'create_countries', 'description' => 'Create Countries'],
            ['name' => 'edit_countries', 'description' => 'Edit Countries'],
            ['name' => 'delete_countries', 'description' => 'Delete Countries'],

            // Categories
            ['name' => 'view_categories', 'description' => 'View Categories'],
            ['name' => 'create_categories', 'description' => 'Create Categories'],
            ['name' => 'edit_categories', 'description' => 'Edit Categories'],
            ['name' => 'delete_categories', 'description' => 'Delete Categories'],

            // Sub Categories
            ['name' => 'view_sub_categories', 'description' => 'View Sub Categories'],
            ['name' => 'create_sub_categories', 'description' => 'Create Sub Categories'],
            ['name' => 'edit_sub_categories', 'description' => 'Edit Sub Categories'],
            ['name' => 'delete_sub_categories', 'description' => 'Delete Sub Categories'],

            // Support Tickets
            ['name' => 'view_tickets', 'description' => 'View Tickets'],
            ['name' => 'reply_tickets', 'description' => 'Reply to Tickets'],
            ['name' => 'delete_tickets', 'description' => 'Delete Tickets'],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'guard_name' => 'admin'
            ]);
        }
    }
}
