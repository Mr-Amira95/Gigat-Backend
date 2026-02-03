<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateFreelancerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'prefix' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('prefix', $this->prefix)
                        ->exists();

                    if ($exists) {
                        $fail(__('unique_with_prefix'));
                    }
                }
            ],
            'gender' => 'required|string|in:male,female,other',
            'profession_id' => 'required|exists:professions,id',
            'country_id' => 'required|exists:countries,id',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable',
            'player_id' => 'nullable',
            'platform' => 'nullable',
            'google_id' => 'nullable',
            'languages' => 'nullable|array',
            'languages.*' => 'integer|exists:languages,id',

            // for freelancer profile
            'bio' => 'required|string|max:500',
            'avatar' => 'nullable|mimes:png,jpg,jpeg',
            'file' => 'nullable|array',
            'file.*' => 'mimes:png,jpg,jpeg,pdf,docx',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
            'category_ids' => 'required'
        ];
    }
}
