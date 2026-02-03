<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
            'category_id' => ['required', 'array'],
            'category_id.*' => ['exists:categories,id'],

            'sub_category_id' => ['required', 'array'],
            'sub_category_id.*' => ['exists:sub_categories,id'],

            'translations.en.title' => ['required', 'string'],
            'translations.ar.title' => ['required', 'string'],

        ];
    }
}
