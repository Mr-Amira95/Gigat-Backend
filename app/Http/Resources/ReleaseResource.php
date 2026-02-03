<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'release_id' => $this->id,
            'android_version' => $this->android_version,
            'ios_version'     => $this->ios_version,
            'web_version'     => $this->web_version,
            'release_note'     => $this->translation?->release_note,
            'is_required'     => (bool) $this->is_required,
            'created_at'    => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
