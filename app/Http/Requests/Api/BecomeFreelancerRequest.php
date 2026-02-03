<?php

namespace App\Http\Requests\Api;

use App\Enums\LanguageLevelEnum;
use Illuminate\Foundation\Http\FormRequest;

class BecomeFreelancerRequest extends FormRequest
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
            'bio' => 'required|string|max:500',
            'avatar' => 'nullable|mimes:png,jpg,jpeg',
            'file' => 'nullable|array',
            'file.*' => 'mimes:png,jpg,jpeg,pdf,docx',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
            'category_ids'=>'required'
        ];
    }
}
