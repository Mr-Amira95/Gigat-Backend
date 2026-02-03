<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true to allow all users to make requests
    }

    public function rules()
    {
        return [
            'sub_category_id' => 'required',
            'currency' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price'           => 'required|numeric|min:1|max:10000',
            'delivery_day'    => 'required|integer|min:1|max:365',
            'revisions'       => 'required|integer|min:0|max:50',
            'source_file' => 'required|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,docx',
        ];
    }
}
