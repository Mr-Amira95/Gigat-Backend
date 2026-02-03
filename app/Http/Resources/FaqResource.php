<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'question'=>$this->translation->question,
            'answer'=>$this->translation->answer,
            'media_path'=>$this->translation->media_path ? url($this->translation->media_path) : null,
            'media_type'=>$this->translation->media_type,
        ];
    }
}
