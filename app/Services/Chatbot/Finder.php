<?php

namespace App\Services\Chatbot;

use App\Models\Faq;
use App\Models\General;
use App\Models\Service;

use Illuminate\Support\Collection;

class Finder
{
    // public function findService(array $keywords = [], string $language = 'en'): Collection
    // {
    //     $lang = $language === 'ar' ? 'ar' : 'en';

    //     $services = Service::with([
    //             'translations' => function ($q) use ($lang) {
    //                 $q->where('language', $lang);
    //             },
    //             'subCategory.translations' => function ($q) use ($lang) {
    //                 $q->where('language', $lang);
    //             },
    //             'subCategory.category.translations' => function ($q) use ($lang) {
    //                 $q->where('language', $lang);
    //             },
    //             'tags.translations' => function ($q) use ($lang) {
    //                 $q->where('language', $lang);
    //             },
    //             'user'
    //         ])
    //         ->where('is_active', true)
    //         ->where(function ($q) use ($keywords, $lang) {

    //             foreach ($keywords as $kw) {

    //                 $q->orWhereHas('translations', function ($tQ) use ($kw, $lang) {
    //                     $tQ->where('language', $lang)
    //                         ->where(function ($inner) use ($kw) {
    //                             $inner->where('title', 'LIKE', "%{$kw}%")
    //                                   ->orWhere('description', 'LIKE', "%{$kw}%");
    //                         });
    //                 });

    //                 $q->orWhereHas('subCategory.translations', function ($subQ) use ($kw, $lang) {
    //                     $subQ->where('language', $lang)
    //                          ->where('title', 'LIKE', "%{$kw}%");
    //                 });

    //                 $q->orWhereHas('subCategory.category.translations', function ($catQ) use ($kw, $lang) {
    //                     $catQ->where('language', $lang)
    //                          ->where('title', 'LIKE', "%{$kw}%");
    //                 });

    //                 $q->orWhereHas('tags.translations', function ($tagQ) use ($kw, $lang) {
    //                     $tagQ->where('language', $lang)
    //                          ->where('title', 'LIKE', "%{$kw}%");
    //                 });
    //             }
    //         })
    //         ->orderByDesc('rating')
    //         ->get();

    //     return $services->map(function ($service) {

    //         $translation = $service->translations->first();
    //         $subTranslation = $service->subCategory->translations->first();
    //         $catTranslation = $service->subCategory->category->translations->first();

    //         return [
    //             "id" => $service->id,
    //             "category" => $catTranslation?->title,
    //             "sub_category" => $subTranslation?->title,
    //             "tags" => $service->tags->map(function ($tag) {
    //                 return $tag->translations->first()?->title;
    //             })->filter()->values()->toArray(),

    //             "title" => $translation?->title,
    //             "description" => $translation?->description,

    //             "cover" => $service->cover
    //                 ? asset($service->cover)
    //                 : null,

    //             "rating" => $service->rating,

    //             "start_service_from" => number_format($service->min_price, 2) . " SAR",
    //             "end_service_to" => number_format($service->max_price, 2) . " SAR",

    //             "delivery_days_from" => $service->min_delivery_days,
    //             "delivery_days_to" => $service->max_delivery_days,

    //             "revisions_from" => $service->min_revisions,
    //             "revisions_to" => $service->max_revisions,

    //             "user" => [
    //                 "id" => $service->user->id,
    //                 "username" => $service->user->username,
    //                 "profession" => $service->user->profession,
    //                 "avatar" => $service->user->avatar
    //                     ? asset($service->user->avatar)
    //                     : null,
    //                 "is_freelancer_verified" => (bool) $service->user->is_verified,
    //                 "company" => $service->user->company
    //             ]
    //         ];
    //     });
    // }
    public function findService(array $keywords = [], string $language = 'en'): Collection
    {
        $lang = $language === 'ar' ? 'ar' : 'en';
        // dd($keywords, $language);
        $query = Service::query()
            ->where('is_active', true);

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords, $lang) {

                // 1) service title/description
                $q->whereHas('translations', function ($tQ) use ($keywords, $lang) {
                    $tQ->where('language', $lang)
                        ->where(function ($inner) use ($keywords) {
                            foreach ($keywords as $kw) {
                                $inner->orWhere('title', 'LIKE', "%{$kw}%")
                                    ->orWhere('description', 'LIKE', "%{$kw}%");
                            }
                        });
                })

                    // 2) subcategory title
                    ->orWhereHas('subCategory.translations', function ($subQ) use ($keywords, $lang) {
                        $subQ->where('language', $lang)
                            ->where(function ($inner) use ($keywords) {
                                foreach ($keywords as $kw) {
                                    $inner->orWhere('title', 'LIKE', "%{$kw}%");
                                }
                            });
                    })

                    // 3) tags title
                    ->orWhereHas('tags.translations', function ($tagQ) use ($keywords, $lang) {
                        $tagQ->where('language', $lang)
                            ->where(function ($inner) use ($keywords) {
                                foreach ($keywords as $kw) {
                                    $inner->orWhere('title', 'LIKE', "%{$kw}%");
                                }
                            });
                    })

                    // ✅ 4) NEW: search inside plan_features (features table)
                    ->orWhereHas('features', function ($fQ) use ($keywords) {
                        $fQ->where(function ($inner) use ($keywords) {
                            foreach ($keywords as $kw) {
                                // match feature type and feature value (delivery_days, revisions, price, 5, 10, etc.)
                                $inner->orWhere('type', 'LIKE', "%{$kw}%")
                                    ->orWhere('value', 'LIKE', "%{$kw}%");
                            }
                        });
                    });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Top Rated Results
        |--------------------------------------------------------------------------
        | Limit results for chatbot usage (performance critical)
        */
        $services = $query
            ->orderByDesc('rating')
            ->limit(10)
            ->get();
            // dd($services);
        /*
        |--------------------------------------------------------------------------
        | Eager Load Relations AFTER Filtering
        |--------------------------------------------------------------------------
        | Prevents unnecessary joins during search filtering
        */
        $services->load([
            'translations' => fn($q) => $q->where('language', $lang),
            'subCategory.translations' => fn($q) => $q->where('language', $lang),
            'subCategory.category.translations' => fn($q) => $q->where('language', $lang),
            'tags.translations' => fn($q) => $q->where('language', $lang),
            'user',
            'features.plan',
        ]);

        return $services;
    }

    public function findFaq(array $keywords = [], string $language = 'en'): Collection
    {

        $lang = ($language === 'ar') ? 'ar' : 'en';

        $faqs = Faq::whereHas('translation', function ($q) use ($keywords, $lang) {
            $q->where('language', $lang)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $query->orWhere('question', 'LIKE', "%{$kw}%")
                            ->orWhere('answer', 'LIKE', "%{$kw}%");
                    }
                });
        })
            ->with(['translation' => function ($q) use ($lang) {
                $q->where('language', $lang);
            }])
            ->limit(5)
            ->get();

        // Transform to only include question and answer
        return $faqs->map(function ($faq) {
            return [
                'question' => $faq->translation->question ?? null,
                'answer' => $faq->translation->answer ?? null,
            ];
        });
    }

    public function findGeneral(): Collection
    {
        return General::all();
    }
}
