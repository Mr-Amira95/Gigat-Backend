<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = app()->getLocale();

        $locale = app()->getLocale();

        $titleJson = json_decode($this->title, true);
        $bodyJson = json_decode($this->body, true);

        $title = $titleJson[$locale] ?? $titleJson['en'] ?? null;
        $body = $bodyJson[$locale] ?? $bodyJson['en'] ?? null;



        return [
            'id'                => $this->id,
            // 'user_id'           => $this->user_id,
            'title'             => $title,
            'body'              => $body,
            'type'              => $this->type,
            'type_id'           => $this->type_id,
            'is_read'           => $this->is_read,
            // 'onesignal_id'      => $this->onesignal_id,
            // 'response_onesignal' => $this->response_onesignal,
            'created_at' => Carbon::parse($this->created_at)->toDayDateTimeString(),
        ];
    }
}
