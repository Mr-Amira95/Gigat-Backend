<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'plans.*.features.*.value' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $type = request()->input(str_replace('.value', '.type', $attribute));
                    if (in_array($type, ['price', 'delivery_days', 'revisions']) && (is_null($value) || (float) $value <= 0)) {
                        $fail("The {$attribute} must be greater than 0.");
                    }
                },
            ],
            'plans.*.features.*.type' => ['required', 'in:additional,price,delivery_days,revisions,source_files'],

            'tags' => ['nullable', 'array'],
            'tags.*' => 'nullable',

            'cover' => 'nullable',
            'media' => 'nullable',
            'array',
            'media.*' => 'nullable',
            'is_active' => 'sometimes|boolean',

        ];
    }
}
