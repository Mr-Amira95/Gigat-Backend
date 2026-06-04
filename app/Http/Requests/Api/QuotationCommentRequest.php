<?php

namespace App\Http\Requests\Api;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class QuotationCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $quotation = Quotation::find($this->input('quotation_id'));

        if (!$quotation) {
            return false;
        }

        $user = Auth::user();

        // Quotation owner (client) or any freelancer may comment.
        return $user->id === $quotation->user_id || $user->freelancer !== null;
    }

    public function rules(): array
    {
        return [
            'comment'      => 'required|string',
            'quotation_id' => 'required|exists:quotations,id',
        ];
    }
}