<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\SubCategory;
use App\Models\SubCategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'icon' => 'categories/graphics-design.svg',
                'max_price' => 1000.00,
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Graphics & Design',
                        'description' => 'Designs to make you stand out',
                        'cover' => 'categories/graphics-design-cover.jpg'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'التصميم والجرافيك',
                        'description' => 'تصاميم تجعلك متميزاً',
                        'cover' => 'categories/graphics-design-cover.jpg'
                    ]
                ],
                'subcategories' => [
                    [
                        'icon' => 'subcategories/logo-design.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'Logo Design'],
                            ['language' => 'ar', 'title' => 'تصميم الشعار']
                        ]
                    ],
                    [
                        'icon' => 'subcategories/web-design.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'Website Design'],
                            ['language' => 'ar', 'title' => 'تصميم المواقع']
                        ]
                    ],
                ]
            ],
            [
                'icon' => 'categories/digital-marketing.svg',
                'max_price' => 2000.00,
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Digital Marketing',
                        'description' => 'Build your brand. Grow your business',
                        'cover' => 'categories/digital-marketing-cover.jpg'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'التسويق الرقمي',
                        'description' => 'ابني علامتك التجارية. نمي عملك',
                        'cover' => 'categories/digital-marketing-cover.jpg'
                    ]
                ],
                'subcategories' => [
                    [
                        'icon' => 'subcategories/social-media.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'Social Media Marketing'],
                            ['language' => 'ar', 'title' => 'التسويق عبر وسائل التواصل']
                        ]
                    ],
                    [
                        'icon' => 'subcategories/seo.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'SEO'],
                            ['language' => 'ar', 'title' => 'تحسين محركات البحث']
                        ]
                    ],
                ]
            ],
            [
                'icon' => 'categories/programming.svg',
                'max_price' => 5000.00,
                'translations' => [
                    [
                        'language' => 'en',
                        'title' => 'Programming & Tech',
                        'description' => 'Development and technical solutions',
                        'cover' => 'categories/programming-cover.jpg'
                    ],
                    [
                        'language' => 'ar',
                        'title' => 'البرمجة والتقنية',
                        'description' => 'حلول تطويرية وتقنية',
                        'cover' => 'categories/programming-cover.jpg'
                    ]
                ],
                'subcategories' => [
                    [
                        'icon' => 'subcategories/web-dev.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'Website Development'],
                            ['language' => 'ar', 'title' => 'تطوير المواقع']
                        ]
                    ],
                    [
                        'icon' => 'subcategories/mobile-dev.svg',
                        'translations' => [
                            ['language' => 'en', 'title' => 'Mobile Development'],
                            ['language' => 'ar', 'title' => 'تطوير تطبيقات الجوال']
                        ]
                    ],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            // Create category
            $category = Category::create([
                'icon' => $categoryData['icon'],
                'max_price' => $categoryData['max_price']
            ]);

            // Create category translations
            foreach ($categoryData['translations'] as $translation) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'language' => $translation['language'],
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'cover' => $translation['cover']
                ]);
            }

            // Create subcategories
            foreach ($categoryData['subcategories'] as $subcategoryData) {
                $subcategory = SubCategory::create([
                    'category_id' => $category->id,
                    'icon' => $subcategoryData['icon']
                ]);

                foreach ($subcategoryData['translations'] as $translation) {
                    SubCategoryTranslation::create([
                        'sub_category_id' => $subcategory->id,
                        'language' => $translation['language'],
                        'title' => $translation['title']
                    ]);
                }
            }
        }
    }
}
