<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RequestCreateRequest extends FormRequest
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

            // Quotation flow
            'quotation_id' => 'nullable|integer|exists:quotations,id|required_without_all:service_id,request_id',
            'comment_id'   => 'nullable|integer|exists:quotation_comments,id|required_with:quotation_id',

            // New Service flow
            'service_id'   => 'nullable|integer|exists:services,id|required_without_all:quotation_id,request_id',
            'plan_id'      => 'nullable|integer|exists:plans,id|required_with:service_id',

            // Existing Request payment flow
            'request_id'   => 'nullable|integer|exists:requests,id|required_without_all:quotation_id,service_id',
        ];
    }
}
