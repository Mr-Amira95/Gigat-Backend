<?php

namespace Database\Seeders;

use App\Models\Slider;
use App\Models\SliderTranslation;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'is_active' => true,
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Welcome to Our App',
                        'description' => 'Discover amazing features and services',
                        'media_path' => 'sliders/slider1-en.jpg',
                        'media_type' => 'image'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'مرحباً بكم في تطبيقنا',
                        'description' => 'اكتشف ميزات وخدمات مذهلة',
                        'media_path' => 'sliders/slider1-ar.jpg',
                        'media_type' => 'image'
                    ]
                ]
            ],
            [
                'is_active' => true,
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Special Offers',
                        'description' => 'Check out our latest deals and promotions',
                        'media_path' => 'sliders/slider2-en.jpg',
                        'media_type' => 'image'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'عروض خاصة',
                        'description' => 'تحقق من أحدث صفقاتنا وعروضنا الترويجية',
                        'media_path' => 'sliders/slider2-ar.jpg',
                        'media_type' => 'image'
                    ]
                ]
            ],
            [
                'is_active' => false, // An inactive slider for testing
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Coming Soon',
                        'description' => 'New features coming to our app',
                        'media_path' => 'sliders/slider3-en.mp4',
                        'media_type' => 'video'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'قريباً',
                        'description' => 'ميزات جديدة قادمة إلى تطبيقنا',
                        'media_path' => 'sliders/slider3-ar.mp4',
                        'media_type' => 'video'
                    ]
                ]
            ]
        ];

        foreach ($sliders as $sliderData) {
            $slider = Slider::create([
                'is_active' => $sliderData['is_active']
            ]);

            foreach ($sliderData['translations'] as $translation) {
                SliderTranslation::create([
                    'slider_id' => $slider->id,
                    'language' => $translation['language'],
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'media_path' => $translation['media_path'],
                    'media_type' => $translation['media_type']
                ]);
            }
        }
    }
}
