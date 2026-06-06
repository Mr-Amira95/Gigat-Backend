<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_uuid'   => ['required', 'string', 'max:64'],
            'platform'       => ['required', 'string', 'in:web,android,ios'],
            'device_type'    => ['nullable', 'string', 'max:50'],
            'device_os'      => ['nullable', 'string', 'max:100'],
            'device_browser' => ['nullable', 'string', 'max:100'],
            'device_model'   => ['nullable', 'string', 'max:100'],
            'country'      => ['nullable', 'string', 'max:100'],
            'user_id'      => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
