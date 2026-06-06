<?php

namespace App\Http\Requests\Api;

use App\Enums\RequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class AddRequestCommentRequest extends FormRequest
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
            'request_id'  => 'required|exists:requests,id',
            'status'      => 'required|in:' . implode(',', RequestStatusEnum::values()),
            'action'      => 'required|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,m4a,zip|max:51200',

        ];
    }
}
