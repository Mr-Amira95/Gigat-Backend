<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $users = [
        [
            'username' => 'bilal',
            'email' => 'bilal.dev@gigat.com',
            'password' => bcrypt('Developer@123'),
            'profession_id' => 1,
            'gender' => 'male',
            'country_id' => 1,
            'phone' => '123456123',
            'prefix' => '+1',
            'verified_at' => now(),
        ],
        [
            'username' => 'abood',
            'email' => 'abood.dev@gigat.com',
            'password' => bcrypt('Developer@123'),
            'profession_id' => 1,
            'gender' => 'male',
            'country_id' => 1,
            'phone' => '123456321',
            'prefix' => '+2',
            'verified_at' => now(),
        ],
       ];
       User::insert($users);
    }
}
