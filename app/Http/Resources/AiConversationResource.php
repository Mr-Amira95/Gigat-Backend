<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AiConversationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'message'  => $this->message,
            'role'     => $this->role,
            'services'   => ServiceResource::collection($this->services),
            'created_at' => Carbon::parse($this->created_at)->toDayDateTimeString(),
        ];
    }
}
