<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],

            'plans' => ['required', 'array'],
            'plans.*.plan_id' => ['required', 'integer'],
            'plans.*.features' => ['required', 'array'],

            'plans.*.features.*.title' => ['required', 'string'],
            'plans.*.features.*.value' => 'nullable',
            'plans.*.features.*.type' => ['required', 'in:additional,price,delivery_days,revisions,source_files'],

            'tags' => ['nullable', 'array'],
            'tags.*' => 'nullable',

            'cover' => 'required',
            'media' => 'required', 'array',
            'media.*' => 'required',
        ];
    }
}
