<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class QuotationCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'comment' => 'required|string', 
            'quotation_id' => 'required|exists:quotations,id', 
        ];
    }
}