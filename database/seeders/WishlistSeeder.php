<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        // Get first few users and services to ensure they exist
        $users = User::take(2)->pluck('id')->toArray();
        $services = Service::take(3)->pluck('id')->toArray();

        if (empty($users) || empty($services)) {
            return; // Skip if no users or services exist
        }

        $wishlists = [
            [
                'user_id' => $users[0],
                'service_id' => $services[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[0],
                'service_id' => $services[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[1],
                'service_id' => $services[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users[1],
                'service_id' => $services[2],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($wishlists as $wishlist) {
            Wishlist::create($wishlist);
        }
    }
}
