<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestLogResource extends JsonResource
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
            'request_id' => $this->request_id,
            'action' => $this->translation->action,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'role' => $this->user->freelancer ? 'freelancer' : 'client',

            ] : null,

            'attachments' => $this->attachments ? $this->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'media_path' => url($attachment->media_path),
                    'media_type' => $attachment->media_type,
                ];
            }) : [],
        ];
    }
}
