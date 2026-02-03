<?php

namespace App\Http\Resources;

use App\Models\Currency;
use App\Utilities\CurrencyConverter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $currencyCode = $request->header('currency', 'USD');

        $netAmount = ($this->amount ?? 0) + ($this->fees ?? 0);

        $convertedAmount = $netAmount
            ? CurrencyConverter::convert($netAmount, 'USD', $currencyCode)
            : 0.0;
        return [
            'request_id'     => $this->request_id,
            'request_title'  => $this->request->title,
            'amount'         => $convertedAmount !== null ? $convertedAmount : 0.0,
            'payment_status' => 'paid',
            'date'        => $this->created_at?->toDateTimeString(),

        ];
    }
}
