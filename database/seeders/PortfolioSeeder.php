<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('freelancer')->get();

        if ($users->isEmpty()) {
            $users = User::take(3)->get();
        }

        foreach ($users as $user) {
            $portfolios = Portfolio::factory()
                ->count(rand(2, 4))
                ->create([
                    'user_id' => $user->id
                ]);

            foreach ($portfolios as $portfolio) {
                $mediaTypes = ['image', 'video', 'document'];
                $mediaItems = [];

                for ($i = 1; $i <= rand(2, 5); $i++) {
                    $mediaType = $mediaTypes[array_rand($mediaTypes)];
                    $extension = $mediaType === 'image' ? 'jpg' : ($mediaType === 'video' ? 'mp4' : 'pdf');

                    $mediaItems[] = $portfolio->media()->create([
                        'media_path' => "storage/portfolio/{$portfolio->id}/{$mediaType}{$i}.{$extension}",
                        'media_type' => $mediaType,
                        'is_cover' => false, 
                    ]);
                }

                
                $coverMedia = collect($mediaItems)->firstWhere('media_type', 'image') ?? $mediaItems[0];
                $coverMedia->update(['is_cover' => true]);
            }
        }
    }
}
