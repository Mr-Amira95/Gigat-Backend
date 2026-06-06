<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_uuid' => ['required', 'string', 'max:64', 'exists:analytics_visitors,visitor_uuid'],
            'event_name'   => ['required', 'string', 'max:100'],
            'screen_name'  => ['nullable', 'string', 'max:100'],
            'metadata'     => ['nullable', 'array'],
        ];
    }
}
