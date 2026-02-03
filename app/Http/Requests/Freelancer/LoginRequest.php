<?php

namespace App\Http\Requests\Freelancer;

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
            'phone' => 'required',
            'password' => 'required|string',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $raw   = (string) $this->input('phone', '');
        $digits = preg_replace('/\D+/', '', $raw);   // keep digits only
        $noLead = ltrim($digits, '0');               // remove leading zeros
        $this->merge(['phone_normalized' => $noLead]);
    }
}
