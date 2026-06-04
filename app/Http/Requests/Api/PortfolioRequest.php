<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PortfolioRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],

            'cover' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'media' => ['nullable', 'array'],
            'media.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,avi,mov', 'max:51200'],
            'service_ids'=>'nullable|array',
            'service_ids.*'=>'nullable',
        ];
    }
}
