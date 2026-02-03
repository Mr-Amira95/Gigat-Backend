<?php

namespace App\Http\Requests\Freelancer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow for authenticated user
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    // public function messages(): array
    // {
    //     return [
    //         'current_password.required' => __('current_password_required'),
    //         'new_password.required' => __('new_password_required'),
    //         'new_password.confirmed' => __('new_password_must_match'),
    //     ];
    // }
}
