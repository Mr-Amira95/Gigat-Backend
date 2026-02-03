<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
        $companyId = optional(auth('api')->user()->freelancer)->company_id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'country_of_registration' => 'sometimes|required|string|max:255',
            'registration_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'registration_number')->ignore($companyId),
            ],

            'contact_email' => 'sometimes|required|email|max:255',
            'contact_phone_number' => 'sometimes|required|string|max:20',
            'website_url' => 'nullable|url|max:255',

            'social_links' => 'nullable|array',
            'social_links.*.icon' => 'required_with:social_links|file|mimes:jpg,jpeg,png,svg,gif',
            'social_links.*.url'  => 'required_with:social_links|url|max:255',
            'social_links.*.title' => 'required_with:social_links|string|max:255',
        ];
    }
}
