<?php

namespace App\Http\Controllers\Freelancer;

use App\Models\General;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Utilities\FileManager;

class GeneralController extends Controller
{
    public function generalInfo()
    {
        $generals = General::whereIn('key', [
            'email',
            'phone',
            'whatsapp',
            'facebook',
            'twitter',
            'instagram',
            'linkedin',
            'youtube',
            'address',
            'platform_title_en',
            'platform_title_ar',
            'platform_description_en',
            'platform_description_ar',
            'platform_logo',
            'tax',
            'comission',
            'fees',
            'commission',
        ])->get();

        $currencies = Currency::all();
        return view('pages.general.index', compact('generals', 'currencies'));
    }

    public function updateGeneralInfo(Request $request)
    {
        $validatedData = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:255',
            'currencies' => 'nullable|array',
            'currencies.*.exchange_rate' => 'required|numeric|min:0',
            'platform_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        if ($request->hasFile('platform_logo')) {

            $filePath = FileManager::upload('logo', $request->file('platform_logo'));

            General::updateOrCreate(
                ['key' => 'platform_logo'],
                ['value' => $filePath]
            );
        }
        foreach ($validatedData['settings'] as $key => $value) {
            General::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        if (!empty($validatedData['currencies'])) {
            foreach ($validatedData['currencies'] as $code => $data) {
                Currency::where('code', $code)->update([
                    'exchange_rate' => $data['exchange_rate'],
                ]);
            }
        }
        return redirect()->back()->with('success', __('general_info_updated_successfully'));
    }

    public function privacyPolicy()
    {
        $privacy = General::whereIn('key', ['privacy_en', 'privacy_ar'])->get()->pluck('value', 'key')->toArray();
        $privacy = array_reverse($privacy);
        return view('pages.general.privacy', compact('privacy'));
    }


    public function updatePrivacyPolicy(Request $request)
    {
        $validatedData = $request->validate([
            'privacy_en' => 'required|string',
            'privacy_ar' => 'required|string',
        ]);

        General::updateOrCreate(
            ['key' => 'privacy_en'],
            ['value' => $validatedData['privacy_en']]
        );

        General::updateOrCreate(
            ['key' => 'privacy_ar'],
            ['value' => $validatedData['privacy_ar']]
        );

        return redirect()->back()->with('success', __('privacy_policy_updated_successfully'));
    }
    public function terms()
    {
        $terms = General::whereIn('key', ['terms_en', 'terms_ar'])->get()->pluck('value', 'key')->toArray();
        $terms = array_reverse($terms);
        return view('pages.general.terms', compact('terms'));
    }


    public function updateTerms(Request $request)
    {
        $validatedData = $request->validate([
            'terms_en' => 'required|string',
            'terms_ar' => 'required|string',
        ]);

        General::updateOrCreate(
            ['key' => 'terms_en'],
            ['value' => $validatedData['terms_en']]
        );

        General::updateOrCreate(
            ['key' => 'terms_ar'],
            ['value' => $validatedData['terms_ar']]
        );

        return redirect()->back()->with('success', __('terms_updated_successfully'));
    }
}
