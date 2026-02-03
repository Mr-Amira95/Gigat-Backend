<?php

namespace App\Http\Resources;

use App\Enums\PaymentStatusEnum;
use App\Utilities\CurrencyConverter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceFreelancerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currencyCode = $request->header('currency', 'USD');

        $isPaid = $this->payment_status === PaymentStatusEnum::PAID->value;
        $type   = $isPaid ? 'transfer' : 'pending';

        // ✅ Freelancer: amount - commission
        $netAmount = ($this->amount ?? 0) - ($this->commission ?? 0);

        $amount = $netAmount
            ? CurrencyConverter::convert($netAmount, 'USD', $currencyCode)
            : null;


        return [
            'type'        => $type,
            'amount'      => $amount,
            'status'      => $this->payment_status,
            'paid_at'    => $isPaid ? optional($this->paid_at)->toDateTimeString() : null,
            'request_id'  => $this->request_id,
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
