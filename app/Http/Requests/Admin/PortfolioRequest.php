<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Purifier\Facades\Purifier;

class PortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => Purifier::clean($this->input('description', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id'=>'required',
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cover' => 'required',
            'media' => 'nullable',
            'array',
            'media.*' => 'nullable',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'nullable',
        ];
    }
}
