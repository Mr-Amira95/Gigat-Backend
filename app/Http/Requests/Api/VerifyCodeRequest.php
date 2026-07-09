<?php

namespace App\Http\Requests\Api;

use App\Rules\ConvertNumbers;
use Illuminate\Foundation\Http\FormRequest;

class VerifyCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+]+$/', 'required_without:email', new ConvertNumbers],
            'code' => 'required|string|size:6'
        ];
    }


}
