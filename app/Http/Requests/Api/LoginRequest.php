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
            'prefix' => ['required', 'string', 'max:5', new ConvertNumbers],
            'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9]+$/', new ConvertNumbers],
            'password' => 'required|string',
        ];
    }

   
}
