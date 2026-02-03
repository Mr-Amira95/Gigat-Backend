<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->translation?->title,
            'description' => $this->translation?->description,
            'button_action'=>$this->button_action ?? null,
            'button_text'=>$this->translation?->button_text ?? null,
            'media_path' => $this->translation?->media_path ? url($this->translation->media_path) : null,
            'media_type' => $this->translation?->media_type,
        ];
    }
}
