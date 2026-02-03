<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestDeliveryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => ['sometimes', 'string'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,m4a',
        ];
    }
}
