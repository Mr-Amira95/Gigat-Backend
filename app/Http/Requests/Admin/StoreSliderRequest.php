<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderRequest extends FormRequest
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
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'button_text_en' => 'nullable|string|max:255',
            'button_text_ar' => 'nullable|string|max:255',
            'button_action' => 'nullable|string|max:255',
            'media_path_en' => 'nullable|file|mimes:jpg,jpeg,png,mp4,svg|max:20480',
            'media_path_ar' => 'nullable|file|mimes:jpg,jpeg,png,mp4,svg|max:20480',
            'platform' => 'required|in:web,mobile',
        ];
    }
}
