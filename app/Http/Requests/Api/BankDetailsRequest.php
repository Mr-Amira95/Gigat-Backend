<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BankDetailsRequest extends FormRequest
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
            'bank_name'      => ['required','string','max:255'],
            'account_number' => ['required','string'],
            'iban'           => ['required','string','max:255'],
            'swift_code'     => ['nullable','string','max:255'],
        ];
    }
}
