<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->translation?->name,
            'description' => $this->translation?->description,
            'logo' => $this->logo ? url($this->logo) : null,
            'country_of_registration' => $this->translation?->country_of_registration,
            'registration_number' => $this->registration_number,
            'contact_email' => $this->contact_email,
            'contact_phone_number' => $this->contact_phone_number,
            'website_url' => $this->website_url,

            'social_links' => $this->socialLinks->map(fn($link) => [
                'id' => $link->id,
                'title' => $link->translation?->title,
                'icon' => $link->icon ? url($link->icon) : null,
                'url' => $link->url,
            ]),
        ];
    }
}
