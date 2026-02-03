<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->translation->comment,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'profession' => $this->user->profession->translation->title,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
            ] : null,
        ];
    }
}

