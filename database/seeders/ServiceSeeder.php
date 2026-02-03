<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Review;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::all();
        $sampleMedia = [
            [
                'path' => 'services/image1.jpg',
                'type' => 'image',
                'is_cover' => true
            ],
            [
                'path' => 'services/image2.jpg',
                'type' => 'image',
                'is_cover' => false
            ],
            [
                'path' => 'services/video1.mp4',
                'type' => 'video',
                'is_cover' => false
            ],
        ];
        $services = [
            [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => 'Web Development Service',
                        'description' => 'Professional web development service including frontend and backend development',
                    ],
                    'ar' => [
                        'title' => 'خدمة تطوير المواقع',
                        'description' => 'خدمة تطوير مواقع احترافية تشمل تطوير الواجهة الأمامية والخلفية',
                    ],
                ],
                'media' => [
                    [
                        'path' => 'services/web-development-1.jpg',
                        'type' => 'image',
                        'is_cover' => true
                    ],
                    [
                        'path' => 'services/web-development-2.jpg',
                        'type' => 'image',
                        'is_cover' => false
                    ],
                    [
                        'path' => 'services/web-development-demo.mp4',
                        'type' => 'video',
                        'is_cover' => false
                    ],
                ],
            ],
            [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => 'Mobile App Development',
                        'description' => 'Complete mobile app development service for iOS and Android platforms',
                    ],
                    'ar' => [
                        'title' => 'تطوير تطبيقات الجوال',
                        'description' => 'خدمة تطوير تطبيقات الجوال الكاملة لمنصات iOS و Android',
                    ],
                ],
                'media' => [
                    [
                        'path' => 'services/mobile-app-1.jpg',
                        'type' => 'image',
                        'is_cover' => true
                    ],
                    [
                        'path' => 'services/mobile-app-2.jpg',
                        'type' => 'image',
                        'is_cover' => false
                    ],
                ],
            ],
            [
                'status' => 'pending',
                'translations' => [
                    'en' => [
                        'title' => 'UI/UX Design Service',
                        'description' => 'Professional UI/UX design service for web and mobile applications',
                    ],
                    'ar' => [
                        'title' => 'خدمة تصميم واجهة المستخدم',
                        'description' => 'خدمة تصميم واجهة وتجربة المستخدم الاحترافية للتطبيقات',
                    ],
                ],
                'media' => [
                    [
                        'path' => 'services/ui-ux-1.jpg',
                        'type' => 'image',
                        'is_cover' => true
                    ],
                    [
                        'path' => 'services/ui-ux-2.jpg',
                        'type' => 'image',
                        'is_cover' => false
                    ],
                ],
            ],
            [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => 'Digital Marketing Services',
                        'description' => 'Comprehensive digital marketing solutions including SEO, social media, and content marketing',
                    ],
                    'ar' => [
                        'title' => 'خدمات التسويق الرقمي',
                        'description' => 'حلول تسويق رقمي شاملة تتضمن تحسين محركات البحث ووسائل التواصل الاجتماعي والتسويق بالمحتوى',
                    ],
                ],
                'media' => $sampleMedia,
            ],
            [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => 'Graphic Design Service',
                        'description' => 'Creative graphic design services for all your branding and marketing needs',
                    ],
                    'ar' => [
                        'title' => 'خدمة التصميم الجرافيكي',
                        'description' => 'خدمات تصميم جرافيك إبداعية لجميع احتياجات العلامة التجارية والتسويق',
                    ],
                ],
                'media' => $sampleMedia,
            ],
            [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => 'Content Writing Services',
                        'description' => 'Professional content writing and copywriting services for websites and marketing materials',
                    ],
                    'ar' => [
                        'title' => 'خدمات كتابة المحتوى',
                        'description' => 'خدمات كتابة محتوى واحترافية للمواقع الإلكترونية والمواد التسويقية',
                    ],
                ],
                'media' => $sampleMedia,
            ],
        ];


        $additionalServices = [
            'Video Editing Service' => 'Professional video editing and post-production services',
            'SEO Optimization' => 'Complete SEO optimization service for better search engine rankings',
            'Social Media Management' => 'Full-service social media management and marketing',
            'Email Marketing Service' => 'Professional email marketing campaign management and optimization',
        ];

        foreach ($additionalServices as $title => $description) {
            $services[] = [
                'status' => 'approved',
                'translations' => [
                    'en' => [
                        'title' => $title,
                        'description' => $description,
                    ],
                    'ar' => [
                        'title' => 'خدمة ' . $title,
                        'description' => 'وصف ' . $description,
                    ],
                ],
                'media' => $sampleMedia,
            ];
        }


        foreach ($services as $serviceData) {
            $randomCategory = Category::inRandomOrder()->first();
            $service = Service::create([
                'sub_category_id' => $randomCategory->id,
                'user_id' => 1,
                'status' => $serviceData['status'],
                'is_recommended' => rand(0, 1),
                'is_featured' => rand(0, 1),
                'rating' => rand(3, 5),
            ]);
            foreach ($serviceData['translations'] as $language => $translation) {
                $service->translations()->create([
                    'language' => $language,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                ]);
            }
            foreach ($serviceData['media'] as $media) {
                $service->media()->create([
                    'media_path' => $media['path'],
                    'media_type' => $media['type'],
                    'is_cover' => $media['is_cover'],
                ]);
            }
            $randomMedia = array_rand($sampleMedia, 2);
            foreach ($randomMedia as $index) {
                $media = $sampleMedia[$index];
                $service->media()->create([
                    'media_path' => $media['path'],
                    'media_type' => $media['type'],
                    'is_cover' => $media['is_cover'],
                ]);
            }

            $tags = Tag::where('category_id', $randomCategory->id)
                ->inRandomOrder()
                ->take(rand(2, 4))
                ->get();

            if ($tags->isNotEmpty()) {
                $service->tags()->attach($tags->pluck('id')->toArray());
            }

            $users = User::where('id', '!=', 1)->inRandomOrder()->take(5)->get();
            foreach ($users as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'rating' => rand(3, 5),
                    'comment' => fake()->paragraph(),
                ]);
            }
        }
        $plans = [
            [
                'price' => 50,
                'delivery_days' => 5,
                'revision' => 3,
                'with_source_code' => true,
                'translations' => [
                    'en' => ['title' => 'Basic'],
                    'ar' => ['title' => 'أساسي'],
                ],
                'features' => [
                    [
                        'value' => '1',
                        'translations' => [
                            'en' => ['title' => 'Source File'],
                            'ar' => ['title' => 'ملف المصدر'],
                        ],
                    ],  
                    [
                        'value' => '3',
                        'translations' => [
                            'en' => ['title' => 'Revisions'],
                            'ar' => ['title' => 'مراجعات'],
                        ],
                    ],
                    [
                        'value' => null,
                        'is_included' => false,
                        'translations' => [
                            'en' => ['title' => 'Commercial Use'],
                            'ar' => ['title' => 'استخدام تجاري'],
                        ],
                    ],
                ],
            ],
            [
                'price' => 100,
                'delivery_days' => 7,
                'revision' => 3,
                'with_source_code' => true,
                'translations' => [
                    'en' => ['title' => 'Standard'],
                    'ar' => ['title' => 'قياسي'],
                ],
                'features' => [
                    [
                        'value' => '2',
                        'translations' => [
                            'en' => ['title' => 'Source Files'],
                            'ar' => ['title' => 'ملفات المصدر'],
                        ],
                    ],
                    [
                        'value' => '5',
                        'translations' => [
                            'en' => ['title' => 'Revisions'],
                            'ar' => ['title' => 'مراجعات'],
                        ],
                    ],
                    [
                        'value' => null,
                        'translations' => [
                            'en' => ['title' => 'Commercial Use'],
                            'ar' => ['title' => 'استخدام تجاري'],
                        ],
                    ],
                ],
            ],
            [
                'price' => 200,
                'delivery_days' => 10,
                'revision' => 3,
                'with_source_code' => true,
                'translations' => [
                    'en' => ['title' => 'Premium'],
                    'ar' => ['title' => 'متميز'],
                ],
                'features' => [
                    [
                        'value' => '3',
                        'translations' => [
                            'en' => ['title' => 'Source Files'],
                            'ar' => ['title' => 'ملفات المصدر'],
                        ],
                    ],
                    [
                        'value' => 'Unlimited',
                        'translations' => [
                            'en' => ['title' => 'Revisions'],
                            'ar' => ['title' => 'مراجعات'],
                        ],
                    ],
                    [
                        'value' => null,
                        'translations' => [
                            'en' => ['title' => 'Commercial Use'],
                            'ar' => ['title' => 'استخدام تجاري'],
                        ],
                    ],
                ],
            ],
        ];
        foreach ($plans as $planData) {
            $plan = Plan::create([
                'price' => $planData['price'],
                'delivery_days' => $planData['delivery_days'],
                'revision' => $planData['revision'],
                'with_source_code' => $planData['with_source_code'],
            ]);
            foreach ($planData['translations'] as $language => $translation) {
                $plan->translations()->create([
                    'language' => $language,
                    'title' => $translation['title'],
                ]);
            }
            $allServices = Service::all();
            foreach ($allServices as $service) {
                if (rand(0, 1)) {
                    $service->plans()->attach($plan->id);
                    foreach ($planData['features'] as $featureData) {
                        $feature = $plan->features()->create([
                            'service_id' => $service->id,
                            'value' => $featureData['value'],
                        ]);
                        foreach ($featureData['translations'] as $language => $translation) {
                            $feature->translations()->create([
                                'language' => $language,
                                'title' => $translation['title'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
