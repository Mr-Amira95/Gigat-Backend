<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists('admins', 'email')->where(function ($query) {
                    $query->where('is_active', 1); // ✅ must be active
                }),
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'email.exists' => 'No active account found with this email address.',
        ];
    }
}
