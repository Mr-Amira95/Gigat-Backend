<?php

namespace App\Http\Resources;

use App\Utilities\FileManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'attachment_url' => asset($this->attachment_url), 
            'type'           => FileManager::getFileTypeFromPath($this->attachment_url), // image, doc, video, etc
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
