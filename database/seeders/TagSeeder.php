<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagsByCategory = [
            'Graphics & Design' => [
                'Logo Design' => 'Professional logo design services',
                'Brand Identity' => 'Complete brand identity design',
                'Business Logo' => 'Custom business logo creation',
                'Minimalist Logo' => 'Clean and minimal logo design',
                'Logo Maker' => 'Quick and efficient logo creation',
            ],
            'Programming & Tech' => [
                'Web Design' => 'Custom website design services',
                'UI Design' => 'User interface design for websites',
                'Responsive Design' => 'Mobile-friendly website design',
                'Landing Page' => 'Converting landing page design',
                'Website Mockup' => 'Professional website mockups',
            ],
            'Digital Marketing' => [
                'Facebook Marketing' => 'Facebook advertising and management',
                'Instagram Marketing' => 'Instagram growth and engagement',
                'Social Media Strategy' => 'Comprehensive social media planning',
                'Content Creation' => 'Social media content creation',
                'Social Media Management' => 'Full social media account management',
            ],
            'SEO' => [
                'Keyword Research' => 'In-depth keyword analysis and research',
                'Link Building' => 'Quality backlink building services',
                'Local SEO' => 'Local business SEO optimization',
                'Technical SEO' => 'Website technical SEO improvements',
                'SEO Optimization' => 'Complete website SEO optimization',
            ],
            'Website Development' => [
                'WordPress Development' => 'Custom WordPress website development',
                'Frontend Development' => 'Frontend website programming',
                'Backend Development' => 'Backend system development',
                'Full Stack Development' => 'Complete web application development',
                'Web Applications' => 'Custom web app development',
            ],
            'Mobile Development' => [
                'Android Development' => 'Native Android app development',
                'iOS Development' => 'Native iOS app development',
                'React Native' => 'Cross-platform React Native development',
                'Flutter Development' => 'Cross-platform Flutter development',
                'Mobile UI Development' => 'Mobile app interface development',
            ],
        ];
        foreach ($tagsByCategory as $categoryTitle => $tags) {
            $category = category::whereHas('translations', function ($query) use ($categoryTitle) {
                $query->where('title', $categoryTitle);
            })->first();
            if ($category) {
                foreach ($tags as $tagName => $description) {
                    $slug = Str::slug($tagName);
                    $tag = Tag::where('slug', $slug)->where('category_id', $category->id)->first();
                    if (!$tag) {
                        $tag = Tag::create([
                            'category_id' => $category->id,
                            'slug' => $slug,
                        ]);
                        $tag->translations()->createMany([
                            [
                                'language' => 'en',
                                'title' => $tagName,
                            ],
                            [
                                'language' => 'ar',
                                'title' => $this->getArabicTranslation($tagName),
                            ],
                        ]);
                    }
                }
            }
        }
    }
    private function getArabicTranslation(string $text): string
    {
        $translations = [
            'Logo Design' => 'تصميم الشعار',
            'Brand Identity' => 'الهوية التجارية',
            'Business Logo' => 'شعار الأعمال',
            'Minimalist Logo' => 'شعار بسيط',
            'Logo Maker' => 'صانع الشعارات',
            'Web Design' => 'تصميم المواقع',
            'UI Design' => 'تصميم واجهة المستخدم',
            'Responsive Design' => 'تصميم متجاوب',
            'Landing Page' => 'صفحة هبوط',
            'Website Mockup' => 'نموذج موقع',
            'Facebook Marketing' => 'تسويق فيسبوك',
            'Instagram Marketing' => 'تسويق انستغرام',
            'Social Media Strategy' => 'استراتيجية التواصل الاجتماعي',
            'Content Creation' => 'إنشاء المحتوى',
            'Social Media Management' => 'إدارة وسائل التواصل',
            'Keyword Research' => 'بحث الكلمات المفتاحية',
            'Link Building' => 'بناء الروابط',
            'Local SEO' => 'تحسين محركات البحث المحلي',
            'Technical SEO' => 'تحسين محركات البحث التقني',
            'SEO Optimization' => 'تحسين محركات البحث',
            'WordPress Development' => 'تطوير ووردبريس',
            'Frontend Development' => 'تطوير الواجهة الأمامية',
            'Backend Development' => 'تطوير الخلفية',
            'Full Stack Development' => 'تطوير الويب المتكامل',
            'Web Applications' => 'تطبيقات الويب',
            'Android Development' => 'تطوير أندرويد',
            'iOS Development' => 'تطوير آي أو إس',
            'React Native' => 'تطوير رياكت نيتف',
            'Flutter Development' => 'تطوير فلاتر',
            'Mobile UI Development' => 'تطوير واجهة الجوال',
        ];
        return $translations[$text] ?? 'وسم ' . $text;
    }
}
// TagSeeder  SQL
// INSERT INTO tags (category_id, slug, created_at, updated_at) VALUES
// (1, 'logo-design', NOW(), NOW()),
// (1, 'brand-identity', NOW(), NOW()),
// (1, 'business-logo', NOW(), NOW()),
// (1, 'minimalist-logo', NOW(), NOW()),
// (1, 'logo-maker', NOW(), NOW()),

// (2, 'web-design', NOW(), NOW()),
// (2, 'ui-design', NOW(), NOW()),
// (2, 'responsive-design', NOW(), NOW()),
// (2, 'landing-page', NOW(), NOW()),
// (2, 'website-mockup', NOW(), NOW()),

// (3, 'facebook-marketing', NOW(), NOW()),
// (3, 'instagram-marketing', NOW(), NOW()),
// (3, 'social-media-strategy', NOW(), NOW()),
// (3, 'content-creation', NOW(), NOW()),
// (3, 'social-media-management', NOW(), NOW())



// TagSeeder Translation SQL
// INSERT INTO tag_translations (tag_id, language, title, created_at, updated_at) VALUES
// -- English Translations
// (2, 'en', 'Logo Design', NOW(), NOW()),
// (3, 'en', 'Brand Identity', NOW(), NOW()),
// (4, 'en', 'Business Logo', NOW(), NOW()),
// (5, 'en', 'Minimalist Logo', NOW(), NOW()),
// (6, 'en', 'Logo Maker', NOW(), NOW()),

// (7, 'en', 'Web Design', NOW(), NOW()),
// (8, 'en', 'UI Design', NOW(), NOW()),
// (9, 'en', 'Responsive Design', NOW(), NOW()),
// (10, 'en', 'Landing Page', NOW(), NOW()),
// (11, 'en', 'Website Mockup', NOW(), NOW()),

// (12, 'en', 'Facebook Marketing', NOW(), NOW()),
// (13, 'en', 'Instagram Marketing', NOW(), NOW()),
// (14, 'en', 'Social Media Strategy', NOW(), NOW()),
// (15, 'en', 'Content Creation', NOW(), NOW()),
// (16, 'en', 'Social Media Management', NOW(), NOW()),

// -- Arabic Translations
// (2, 'ar', 'تصميم الشعار', NOW(), NOW()),
// (3, 'ar', 'الهوية التجارية', NOW(), NOW()),
// (4, 'ar', 'شعار الأعمال', NOW(), NOW()),
// (5, 'ar', 'شعار بسيط', NOW(), NOW()),
// (6, 'ar', 'صانع الشعارات', NOW(), NOW()),

// (7, 'ar', 'تصميم المواقع', NOW(), NOW()),
// (8, 'ar', 'تصميم واجهة المستخدم', NOW(), NOW()),
// (9, 'ar', 'تصميم متجاوب', NOW(), NOW()),
// (10, 'ar', 'صفحة هبوط', NOW(), NOW()),
// (11, 'ar', 'نموذج موقع', NOW(), NOW()),

// (12, 'ar', 'تسويق فيسبوك', NOW(), NOW()),
// (13, 'ar', 'تسويق انستغرام', NOW(), NOW()),
// (14, 'ar', 'استراتيجية وسائل التواصل', NOW(), NOW()),
// (15, 'ar', 'إنشاء المحتوى', NOW(), NOW()),
// (16, 'ar', 'إدارة وسائل التواصل', NOW(), NOW());
