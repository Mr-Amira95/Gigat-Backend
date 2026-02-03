<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prefix' => 'required|string',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password'
        ];
    }

   
}
