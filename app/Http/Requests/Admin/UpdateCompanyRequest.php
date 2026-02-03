<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // $companyId = $this->route('company');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'country_of_registration' => 'required|string|max:255',

            'registration_number' => [
                'required',
                'string',
                'max:255',
                // Rule::unique('companies', 'registration_number')->ignore($companyId),
            ],

            'contact_email' => 'required|email|max:255',
            'contact_phone_number' => 'required|string|max:20',
            'website_url' => 'nullable|url|max:255',

            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',

            'user_id' => 'required|exists:users,id',

            'social_links' => 'nullable|array',
            'social_links.*.icon' => 'nullable|file|mimes:jpg,jpeg,png,svg,gif|required_with:social_links.*.title',
            'social_links.*.url' => 'nullable|url|max:255',
            'social_links.*.title' => 'nullable|string|max:255|required_with:social_links.*.icon',
        ];
    }
}
