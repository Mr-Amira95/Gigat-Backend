<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utilities\CurrencyConverter;
use App\Models\Currency;

class PlanResource extends JsonResource
{
    // use CurrencyConverter;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currencyCode = $request->header('currency', 'USD');
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $symbol = $currencyModel ? $currencyModel->symbol : '$';

        return [
            'id' => $this->id,
            'title' => $this->translation->title,
            'features' => $this->features->map(function ($feature) use ($currencyCode, $symbol) {
                $value = $feature->value;

                if (strtolower($feature->translation->title) === 'price') {
                    $convertedPrice = $feature->value
                        ? CurrencyConverter::convert($feature->value, 'USD', $currencyCode)
                        : null;
                    $value = $convertedPrice !== null ? (float)$convertedPrice : null;
                }

                if ($feature->type === 'source_files') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                return [
                    'id' => $feature->id,
                    'title' => $feature->translation->title,
                    'key' => $feature->type,
                    'value' => (float) $value,
                ];
            }),
        ];
    }
}
