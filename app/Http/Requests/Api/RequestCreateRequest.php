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
            'quotation_id' => 'nullable|integer|exists:quotations,id|required_without:service_id',
            'comment_id'   => 'nullable|integer|exists:quotation_comments,id|required_with:quotation_id',

            // Service flow
            'service_id'   => 'nullable|integer|exists:services,id|required_without:quotation_id',
            'plan_id'      => 'nullable|integer|exists:plans,id|required_with:service_id',


        ];
    }
}
