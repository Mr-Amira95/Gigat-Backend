<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HireFreelancerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {

        return [
            'freelancer_id'      => 'required|exists:users,id',
            'service_title'      => 'required|string|max:255',
            'service_description'=> 'required|string',
            'delivery_days'      => 'required|integer|min:1',
            'price'              => 'required|numeric|min:0',
            'currency'           => 'required|string|max:10',
            'revisions'          => 'required|integer|min:0',
            'source_files'       => 'required|boolean',
            'attachments'        => 'nullable|array',
            'attachments.*'      => 'file|mimes:jpg,jpeg,png,pdf,docx',
        ];
    }
}
