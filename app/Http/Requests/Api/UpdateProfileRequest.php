<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use App\Enums\LanguageLevelEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'sometimes|string|max:255',
            'gender' => ['sometimes', new Enum(GenderEnum::class)],
            'profession_id' => 'sometimes|exists:professions,id',
            'country_id' => 'sometimes|exists:countries,id',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg',
            'description' => 'sometimes|string|max:1000',
            'languages' => 'sometimes|array',
            'languages.*' => 'exists:languages,id',
            'email' => 'sometimes|email|unique:users,email,' . $this->user()->id,

            // 'languages.*.language_id' => 'nullable:languages|exists:languages,id',
            // 'languages.*.level' => 'nullable|string|in:' . implode(',', LanguageLevelEnum::values()),
        ];
    }

}
