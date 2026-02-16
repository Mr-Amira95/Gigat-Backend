<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cover = $this->media->where('is_cover', true)->first();

        return [
            'id' => $this->id,
            'title' => $this->translation->title,
            'user_id' => $this->user_id,
            'cover' => $cover ? [
                'id' => $cover->id,
                'media_path' => $cover->media_path ? url($cover->media_path) : null,
                'media_type' => $cover->media_type,
            ] : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'profession' => $this->user->profession ? $this->user->profession->translation->title : null,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'is_freelancer_verified' => optional($this->user->freelancer)->status === 'verified',
                'company' => ($this->user->freelancer && $this->user->freelancer->company_id)
                    ? new CompanyResource($this->user->freelancer->company)
                    : null,
            ] : null,
        ];
    }
}
