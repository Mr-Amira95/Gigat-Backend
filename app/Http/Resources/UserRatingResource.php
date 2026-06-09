<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRatingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'rating'     => $this->rating,
            'review'     => $this->review,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'rater'      => $this->rater ? [
                'id'       => $this->rater->id,
                'username' => $this->rater->username,
                'avatar'   => $this->rater->avatar ? url($this->rater->avatar) : null,
            ] : null,
        ];
    }
}
