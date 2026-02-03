<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'icon' => $this->icon ? url($this->icon) : null,
            'title' => $this->translation->title,
            'description' => $this->translation->description,
            'cover' => $this->translation->cover ? url($this->translation->cover) : null,
        ];
    }
}
