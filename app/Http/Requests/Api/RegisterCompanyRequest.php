<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | USER REGISTRATION
            |--------------------------------------------------------------------------
            */
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'prefix' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('prefix', $this->prefix)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(__('unique_with_prefix'));
                    }
                }
            ],
            'gender' => 'required|string|in:male,female,prefer_not_say',
            'profession_id' => 'required|exists:professions,id',
            'country_id' => 'required|exists:countries,id',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable',
            'player_id' => 'nullable',
            'platform' => 'nullable',
            'google_id' => 'nullable',
            'languages' => 'nullable|array',
            'languages.*' => 'integer|exists:languages,id',

            /*
            |--------------------------------------------------------------------------
            | FREELANCER PROFILE
            |--------------------------------------------------------------------------
            */
            'bio' => 'required|string|max:500',
            'file' => 'nullable|array',
            'file.*' => 'mimes:png,jpg,jpeg,pdf,docx',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
            'category_ids.*' => 'required|integer|exists:categories,id',


            /*
            |--------------------------------------------------------------------------
            | COMPANY
            |--------------------------------------------------------------------------
            */
            'company_name' => 'required|string|max:255',
            'company_description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'country_of_registration' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:companies,registration_number',
            'contact_email' => 'required|email|max:255',
            'contact_phone_number' => 'required|string|max:20',
            'website_url' => 'nullable|url|max:255',

            // Optional social links
            'social_links' => 'nullable|array',
            'social_links.*.icon' => 'required_with:social_links|file|mimes:jpg,jpeg,png,svg,gif',
            'social_links.*.url' => 'required_with:social_links|url|max:255',
            'social_links.*.title' => 'required_with:social_links|string|max:255',

        ];
    }
}
