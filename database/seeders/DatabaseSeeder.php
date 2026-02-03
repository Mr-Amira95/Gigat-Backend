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
            ProfessionSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            CountriesSeeder::class,
            AdminSeeder::class,
            LanguageSeeder::class,
            GeneralSeeder::class,
            // UserSeeder::class,
            // SliderSeeder::class,
            // CategorySeeder::class,
            // TagSeeder::class,
            // ServiceSeeder::class,
            // WishlistSeeder::class,
            // NotificationSeeder::class,
            // TagSeeder::class,
            // PortfolioSeeder::class,
            // TicketSupportSeeder::class, 
        ]);
    }
}
