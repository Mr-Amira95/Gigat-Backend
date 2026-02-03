<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = [
            'id' => $this->id,
            'title' => $this->translation?->title,
            'type' => $this->type,
        ];
        if ($this->type === 'range') {
            $result['min'] = $this->min_value;
            $result['max'] = $this->max_value;
        }
        $result['options'] = FilterOptionResource::collection($this->options);
        return $result;
    }
}
