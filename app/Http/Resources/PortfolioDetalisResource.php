<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioDetalisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->translation->title,
            'description' => $this->translation->description,
            'media' => PortfolioMediaResource::collection($this->media),
            'service' => ServiceResource::collection($this->services),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'profession' => $this->user->profession ? $this->user->profession->translation->title : null,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'company' => ($this->user->freelancer && $this->user->freelancer->company_id)
                    ? new CompanyResource($this->user->freelancer->company)
                    : null,
            ] : null,
        ];
    }
}
