<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Purifier\Facades\Purifier;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'answer_en' => Purifier::clean($this->input('answer_en', '')),
            'answer_ar' => Purifier::clean($this->input('answer_ar', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'question_en' => 'required|string|max:255',
            'question_ar' => 'required|string|max:255',
            'answer_en' => 'required|string',
            'answer_ar' => 'required|string',
            'category_id' => 'required',    
            'media' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:102400',
        ];
    }
}
