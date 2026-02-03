<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $generals = [
            ['key' => 'email', 'value' => 'info@example.com'],
            ['key' => 'phone', 'value' => '+1234567890'],
            ['key' => 'whatsapp', 'value' => '+1234567890'],
            ['key' => 'facebook', 'value' => 'https://facebook.com/example'],
            ['key' => 'twitter', 'value' => 'https://twitter.com/example'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/example'],
            ['key' => 'linkedin', 'value' => 'https://linkedin.com/company/example'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/@example'],
            ['key' => 'address', 'value' => '123 Main Street, City, Country'],
            ['key' => 'privacy_en', 'value' => 'This is the privacy policy in English.'],
            ['key' => 'privacy_ar', 'value' => 'هذه هي سياسة الخصوصية باللغة العربية.'],
            ['key' => 'terms_en', 'value' => 'These are the terms and conditions in English.'],
            ['key' => 'terms_ar', 'value' => 'هذه هي الشروط والأحكام باللغة العربية.'],
            ['key' => 'about_en', 'value' => 'This is a brief description about our company in English.'],
            ['key' => 'about_ar', 'value' => 'هذا وصف مختصر عن شركتنا باللغة العربية.'],
            ['key' => 'platform_title_en', 'value' => 'Example Company'],
            ['key' => 'platform_title_ar', 'value' => 'Example Company'],
            ['key' => 'platform_description_en', 'value' => 'This is a brief description of the website.'],
            ['key' => 'platform_description_ar', 'value' => 'This is a brief description of the website.'],
            ['key' => 'platform_logo', 'value' => ''],
           
        ];

        DB::table('generals')->insert($generals);
    }
}
