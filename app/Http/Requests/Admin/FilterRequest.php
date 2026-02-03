<?php

namespace App\Http\Requests\Admin;

use App\Enums\FilterTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'type' => ['required', new Enum(FilterTypeEnum::class)],
            'min_value' => 'required_if:type,range|min:0',
            'max_value' => 'required_if:type,range|min:0',
            'translations.*.title' => 'required_if:type,dropdown,dropdown_multiple|max:255',
            'options.*.translations.*.title' => 'required_if:type,dropdown,dropdown_multiple|max:255',
        ];
    }
}
