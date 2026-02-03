<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class FreelancerBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $last4 = $this->account_number
        //     ? substr(preg_replace('/\D+/', '', $this->account_number), -4)
        //     : null;

        // $masked = $last4 ? '**** **** **** ' . $last4 : null;

        return [
            'bank_name'      => $this->bank_name,
            'account_number' => $this->account_number,
            'iban'           => $this->iban,
            'swift_code'     => $this->swift_code,
        ];
    }
}
