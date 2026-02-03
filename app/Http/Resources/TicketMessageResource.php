<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
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
            'ticket_id' => (int)$this->ticket_id,
            'message' => $this->message,
            'is_admin' => boolval($this->is_admin),
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'sender' => [
                'id' => $this->messageable->id,
                'username' => $this->messageable->username ?? $this->messageable->username,
                'avatar' => $this->messageable->avatar ? url($this->messageable->avatar) : null,
            ],
            'attachments' => $this->attachments->map(fn($attachment) => [
                'id' => $attachment->id,
                'message_id' => $attachment->message_id,
                'user_id' => $attachment->user_id,
                'media_path' => $attachment->file_path ? url($attachment->file_path) : null,
                'media_type' => $attachment->file_type,
                'created_at' => Carbon::parse($attachment->created_at)->toDateTimeString(),
            ])
        ];
    }
}
