<?php

namespace App\Http\Requests\Api;

use App\Rules\ConvertNumbers;
use Illuminate\Foundation\Http\FormRequest;

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
            'email' => ['nullable', 'string', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+]+$/', 'required_without:email', new ConvertNumbers],
            'password' => 'required|string',
        ];
    }

   
}
