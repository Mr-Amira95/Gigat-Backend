<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Enums\FreelancerStatusEnum;
use App\Models\PlayerId;
use App\Models\UserRating;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $langCode = request()->header('Accept-Language', 'en'); // default 'en'
        $is_notifiable = (int) PlayerId::where('user_id', $this->id)->where('player_id', $request->player_id)->value('is_notifiable');
        $titleField = 'title_' . $langCode;

        // $isVerified = $this->verified_at ? true : false;
        // $hasBankData = $this->bank ? true : false;

        $accountStatus = $this->bank
            ? 'active'
            : 'inactive';

        $data = [
            'id' => $this->id,
            'name' => $this->username,
            'is_verified' => $this->verified_at ? true : false,
            'verified_at' => $this->verified_at,
            'account_status' => $accountStatus,
            'email' => $this->email,
            'full_phone' => $this->full_phone,
            'prefix' => $this->prefix,
            'phone' => $this->phone,
            'gender' => $this->gender_label,
            'is_notifiable' => $is_notifiable,
            'profession' => $this->profession->translation->title ?? null,
            'rating' => round((float) (UserRating::where('ratee_id', $this->id)->avg('rating') ?? 0), 1),
            'profession_object' => [
                'id' => $this->profession->id ?? null,
                'title' => $this->profession->translation->title ?? null,
            ],
            'country' => $this->country->title ?? null,
            'country_object' => [
                'id' => $this->country->id ?? null,
                'title' => $this->country->title ?? null,
                'currency_code' => $this->country->currency_code ?? null, // <- this is all you need

            ],
            'avatar' => $this->avatar ? url($this->avatar) : null,
            // 'role' => $this->freelancer ? 'freelancer' : 'client',
            'role' => $this->freelancer
                ? ($this->freelancer->company_id ? 'company' : 'freelancer')
                : 'client',

            'languages' => $this->languages->map(fn($lang) => [
                'id' => $lang->language->id,
                'title' => $lang->language->{$titleField} ?? $lang->language->title_en,
                'flag' => $lang->language->flag,
                'level' => $lang->level,
            ])
        ];

        // Add company details if freelancer is linked to one
        if ($this->freelancer && $this->freelancer->company_id) {
            $data['company'] = new CompanyDetailsResource($this->freelancer->company);
        }
        return $data;
    }
}
