<?php

namespace App\Http\Resources;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class FeaturedServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $coverMedia = $this->media->where('is_cover', true)->first();
        return [
            'id' => $this->id,
            'cover' => $coverMedia ? url($coverMedia->media_path) : null,
            'rating' => (float) ($this->rating ?? 0),
            'user' =>
            [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'is_freelancer_verified' => optional($this->user->freelancer)->status === 'verified',
                'company' => ($this->user->freelancer && $this->user->freelancer->company_id)
                    ? new CompanyResource($this->user->freelancer->company)
                    : null,
            ],
        ];
    }
}
