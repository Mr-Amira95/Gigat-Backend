<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class RequestDeliveryResource extends JsonResource
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
            'message' => $this->translation?->message,
            'link' => URL::signedRoute(
                'requests.delivery.files',
                ['id' => $this->request_id]
            ),
            'attachments' => RequestDeliveryAttachmentResource::collection($this->attachments),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
