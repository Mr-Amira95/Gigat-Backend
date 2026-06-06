<?php

namespace App\Http\Controllers\Api;

use App\Models\General;
use Illuminate\Http\Request;
use App\Enums\LanguageLevelEnum;
use App\Services\CountryService;
use App\Services\LanguageService;
use App\Services\ProfessionService;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PlanListResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ProfessionResource;
use App\Http\Resources\ReleaseResource;
use App\Models\ChatMessage;
use App\Services\NotificationService;
use App\Services\PlanService;
use App\Services\ReleaseService;
use Illuminate\Support\Facades\Auth;

class SharedController extends Controller
{
    protected $countryService;
    protected $languageService;
    protected $professionService;
    protected $notificationService;
    protected $planService;
    protected $releaseService;

    public function __construct(CountryService $countryService, LanguageService $languageService, ProfessionService $professionService, NotificationService $notificationService, PlanService $planService, ReleaseService $releaseService)
    {
        $this->countryService = $countryService;
        $this->languageService = $languageService;
        $this->professionService = $professionService;
        $this->notificationService = $notificationService;
        $this->planService = $planService;
        $this->releaseService = $releaseService;
    }
    public function getCountries()
    {
        $perPage = request('per_page', null);
        $search = request('search', null);

        $countries = $this->countryService->getAllActive($perPage, $search);

        return $this->successResponse(__('countries_retrieved'), [
            'countries' => CountryResource::collection($countries),
        ]);
    }

    public function getLanguages()
    {
        $perPage = request('per_page', null);
        $languages = $this->languageService->getAllActive($perPage);
        return $this->successResponse(__('languages_retrieved'), [
            'languages' => LanguageResource::collection($languages),
            'levels' => collect(LanguageLevelEnum::cases())->map(fn($level) => [
                'key' => $level->value,
                'value' => $level->label(),
            ])->values(),
        ]);
    }
    public function getRegisterData()
    {
        $countries = $this->countryService->getAllActive();
        $languages = $this->languageService->getAllActive();
        $professions = $this->professionService->getAllActive();
        return $this->successResponse(__('general_data_retrieved'), [
            'professions' => ProfessionResource::collection($professions),
            'countries' => CountryResource::collection($countries),
            'languages' => [
                'data' => LanguageResource::collection($languages),
                'levels' => collect(LanguageLevelEnum::cases())->map(fn($level) => [
                    'key' => $level->value,
                    'value' => $level->label(),
                ])->values(),

            ],
        ]);
    }


    public function generalData(Request $request)
    {
        // Normalize and fallback to 'en' if not 'en' or 'ar'
        $localeHeader = strtolower(substr($request->header('Accept-Language', 'en'), 0, 2));
        $locale = in_array($localeHeader, ['en', 'ar']) ? $localeHeader : 'en';

        // $settings = General::all()->mapWithKeys(function ($setting) use ($locale) {
        //     $key = $setting->key;

        //     if (preg_match('/_(en|ar)$/', $key, $matches)) {
        //         $baseKey = str_replace($matches[0], '', $key);
        //         if ($matches[1] === $locale) {
        //             return [$baseKey => $setting->value];
        //         }
        //     } else {
        //         return [$key => $setting->value];
        //     }

        //     return [];
        // });
        $settings = General::all()->mapWithKeys(function ($setting) use ($locale) {
            $key = $setting->key;
            $value = $setting->value;

            // Handle multilingual keys (_en, _ar)
            if (preg_match('/_(en|ar)$/', $key, $matches)) {
                $baseKey = str_replace($matches[0], '', $key);
                if ($matches[1] === $locale) {
                    $key = $baseKey;
                } else {
                    return []; // skip other languages
                }
            }

            // Add app.url only for platform_logo
            if ($key === 'platform_logo' && $value) {
                $value = config('app.url') . '/' . ltrim($value, '/');
            }

            return [$key => $value];
        });


        $settings['unread_notifications'] = $this->notificationService->getUnreadNotifications();

        $userId = Auth::id() ?? null;
        $totalUnreadMessages = ChatMessage::where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->whereHas('chat', function ($q) use ($userId) {
                $q->where('user_id_one', $userId)
                    ->orWhere('user_id_two', $userId);
            })
            ->count();


        $settings['unread_messages'] = $totalUnreadMessages ?? 0;

        // Retrieve last active release
        $lastRelease = $this->releaseService->getLatest();
        $settings['release'] = $lastRelease ? new ReleaseResource($lastRelease) : null;


        return $this->successResponse(__('general_data_retrieved'), $settings);
    }

    public function getPlans()
    {
        $plans = $this->planService->index();
        return $this->successResponse('success', PlanListResource::collection($plans));
    }
}
