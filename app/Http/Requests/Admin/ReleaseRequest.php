<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'android_version' => ['required', 'string', 'max:50'],
            'ios_version'     => ['required', 'string', 'max:50'],
            'web_version'     => ['required', 'string', 'max:50'],
            'release_note_en'    => ['required', 'string'],
            'release_note_ar'    => ['required', 'string'],
            'is_required'     => ['sometimes', 'boolean'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }

    // public function attributes(): array
    // {
    //     return [
    //         'android_version' => __('android_version'),
    //         'ios_version'     => __('ios_version'),
    //         'web_version'     => __('web_version'),
    //         'release_note'    => __('release_note'),
    //         'is_required'     => __('is_required'),
    //         'is_active'       => __('status'),
    //     ];
    // }
}
