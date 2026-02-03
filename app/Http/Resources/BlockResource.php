<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            // 'blocker_id'    => $this->blocker_id,
            'blocked_id'    => $this->blocked_id,
            // 'blocker_type'  => $this->blocker_type,
            'blocked_type'  => $this->blocked_type,
            'blocked_user'  => $this->blockedUser ? [
                'id'       => $this->blockedUser->id,
                'name'     => $this->blockedUser->username,
                'avatar' => $this->blockedUser->avatar ? url($this->blockedUser->avatar) : null,
                'profession' => optional(optional($this->blockedUser->profession)->translation)->title,
            ] : null,

            'blocked_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
