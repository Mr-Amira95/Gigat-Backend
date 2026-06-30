<?php

namespace App\Http\Resources;

use App\Models\Wishlist;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Utilities\CurrencyConverter;
use App\Models\Currency;
use App\Models\PlanFeature;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = Auth::guard('api')->id();
        $coverMedia = $this->media->where('is_cover', true)->first();
        $isWishlist = $userId ? Wishlist::where('user_id', $userId)->where('service_id', $this->id)->exists() : false;
        // $minPrice = PlanFeature::where('service_id', $this->id)
        //     ->where('type', 'price')
        //     ->min('value');
        $minPrice = PlanFeature::selectRaw('MIN(CAST(REPLACE(value, ",", "") AS DECIMAL(10,2))) as min_price')
            ->where('service_id',  $this->id)
            ->where('type', 'price')
            ->value('min_price');

        $currencyCode = $request->header('currency', 'USD');
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $symbol = $currencyModel ? $currencyModel->symbol : '$';
        $convertedPrice = $minPrice ? CurrencyConverter::convert($minPrice, 'USD', $currencyCode) : null;
        // dd($minPrice);

        return [
            'id' => $this->id,
            'sub_category_id' => $this->sub_category_id,
            'title' => $this->translation?->title,
            'description' => $this->translation?->description,
            'cover' => $coverMedia ? url($coverMedia->media_path) : null,
            'is_recommended' => boolval($this->is_recommended),
            'is_active' => boolval($this->is_active),
            'is_wishlist' => boolval($isWishlist),
            'rating' => (float) ($this->rating ?? 0),
            'start_service_from' => $convertedPrice ? $convertedPrice . ' ' . $symbol : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'profession' => $this->user->profession->translation->title,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'is_freelancer_verified' => optional($this->user->freelancer)->status === 'verified',
                'company' => ($this->user->freelancer && $this->user->freelancer->company_id)
                    ? new CompanyResource($this->user->freelancer->company)
                    : null,
            ] : null,
        ];
    }
}
