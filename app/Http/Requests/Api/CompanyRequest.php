<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
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
