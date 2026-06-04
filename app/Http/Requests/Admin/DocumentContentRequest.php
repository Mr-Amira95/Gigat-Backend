<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Purifier\Facades\Purifier;

class DocumentContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'content_en' => Purifier::clean($this->input('content_en', '')),
            'content_ar' => Purifier::clean($this->input('content_ar', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'content_en' => ['required', 'string'],
            'content_ar' => ['required', 'string'],
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
        ];
    }
}
