<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
        ];

        if ($this->isMethod('POST')) {
            $rules['icon'] = 'required|file';
        } else {
            $rules['icon'] = 'nullable|file';
        }

        return $rules;
    }
}
