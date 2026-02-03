<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            [
                'translations' => [
                    'en' => 'Software Developer',
                    'ar' => 'مطور برمجيات'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Doctor',
                    'ar' => 'طبيب'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Graphic Designer',
                    'ar' => 'مصمم غرافيك'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Engineer',
                    'ar' => 'مهندس'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Freelancer',
                    'ar' => 'مستقل'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Teacher',
                    'ar' => 'معلم'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Photographer',
                    'ar' => 'مصور'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Content Creator',
                    'ar' => 'منشئ محتوى'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Virtual Assistant',
                    'ar' => 'مساعد افتراضي'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Project Manager',
                    'ar' => 'مدير مشروع'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Marketing Specialist',
                    'ar' => 'أخصائي تسويق'
                ]
            ],
            [
                'translations' => [
                    'en' => 'UI/UX Designer',
                    'ar' => 'مصمم واجهة المستخدم/تجربة المستخدم'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Translator',
                    'ar' => 'مترجم'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Web Developer',
                    'ar' => 'مطور ويب'
                ]
            ],
            [
                'translations' => [
                    'en' => 'SEO Specialist',
                    'ar' => 'أخصائي تحسين محركات البحث'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Social Media Manager',
                    'ar' => 'مدير وسائل التواصل الاجتماعي'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Copywriter',
                    'ar' => 'كاتب محتوى'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Data Analyst',
                    'ar' => 'محلل بيانات'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Accountant',
                    'ar' => 'محاسب'
                ]
            ],
            [
                'translations' => [
                    'en' => 'Customer Support',
                    'ar' => 'دعم العملاء'
                ]
            ],
        ];

        foreach ($professions as $profession) {
            // Create profession
            $professionId = DB::table('professions')->insertGetId([
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create translations
            foreach ($profession['translations'] as $language => $title) {
                DB::table('profession_translations')->insert([
                    'profession_id' => $professionId,
                    'language' => $language,
                    'title' => $title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
