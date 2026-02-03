<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
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
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'button_text_en' => 'nullable|string|max:255',
            'button_text_ar' => 'nullable|string|max:255',
            'button_action' => 'nullable|string|max:255',
            'media_path_en' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480',
            'media_path_ar' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480',
            'platform' => 'required|in:web,mobile',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title_en' => __('english_title'),
            'title_ar' => __('arabic_title'),
            'description_en' => __('english_description'),
            'description_ar' => __('arabic_description'),
            'button_text_en' => __('english_button_text'),
            'button_text_ar' => __('arabic_button_text'),
            'button_action' => __('button_action'),
            'media_path_en' => __('english_media_path'),
            'media_path_ar' => __('arabic_media_path'),
            'media_type_en' => __('english_media_type'),
            'media_type_ar' => __('arabic_media_type'),
            'platform' => __('platform'),
            'is_active' => __('status')
        ];
    }
} 