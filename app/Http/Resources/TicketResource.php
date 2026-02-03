<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\TicketStatusEnum;
use Carbon\Carbon;

class TicketResource extends JsonResource
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
            'subject' => $this->subject,
            'status' => TicketStatusEnum::tryFrom($this->status)?->label(),
            // 'request_title' => $this->request ? $this->request->service->translation->title : null,
            'request_title' => $this->request ? $this->request->translation->title : null,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
            ],
        ];
    }
}
