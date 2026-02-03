<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run()
    {
        
        $faq = Faq::create([
            'faqable_id' => null , 
            'faqable_type' => null, 
            'is_active' => true
        ]);

        
        FaqTranslation::create([
            'faq_id' => $faq->id,
            'language' => 'en',
            'question' => 'What is your return policy?',
            'answer' => 'Our return policy is 30 days with a full refund.',
            'media_path' => 'path/to/your/media/file.jpg', 
            'media_type' => 'image' 
        ]);

        FaqTranslation::create([
            'faq_id' => $faq->id,
            'language' => 'ar',
            'question' => 'ما هي سياسة الإرجاع الخاصة بكم؟',
            'answer' => 'سياسة الإرجاع الخاصة بنا هي 30 يومًا مع استرداد كامل.',
            'media_path' => 'path/to/your/media/file.jpg', 
            'media_type' => 'image' 
        ]);

        
        $faq2 = Faq::create([
            'faqable_id' => 2, 
            'faqable_type' => 'App\Models\Category', 
            'is_active' => true
        ]);

        
        FaqTranslation::create([
            'faq_id' => $faq2->id,
            'language' => 'en',
            'question' => 'How do I contact customer support?',
            'answer' => 'You can contact customer support via email at support@example.com.',
            'media_path' => 'path/to/your/media/file.jpg',
            'media_type' => 'image'
        ]);

        FaqTranslation::create([
            'faq_id' => $faq2->id,
            'language' => 'ar',
            'question' => 'كيف أتواصل مع دعم العملاء؟',
            'answer' => 'يمكنك التواصل مع دعم العملاء عبر البريد الإلكتروني support@example.com.',
            'media_path' => 'path/to/your/media/file.jpg',
            'media_type' => 'image'
        ]);
    }
}
