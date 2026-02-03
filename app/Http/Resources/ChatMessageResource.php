<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $chat = $this->chat;

        $receiver = $chat->user_id_one == $this->sender->id ? $chat->userTwo : $chat->userOne;

        return [
            'id' => $this->id,
            'message' => $this->message,
            'attachment_url' => $this->attachment_url,
            'attachment_type' => $this->attachment_type,
            'is_read' => $this->is_read,
            'created_at' => Carbon::parse($this->created_at)->toDayDateTimeString(),
            // 'created_at' => $this->getRawOriginal('created_at'),
            // 'sender' => [
            //     'id' => $this->sender->id,
            //     'username' => $this->sender->username,
            //     'avatar' => url($this->sender->avatar),
            // ],
            // 'receiver' => [
            //     'id'       => $receiver->id,
            //     'username' => $receiver->username,
            //     'avatar' => url($receiver->avatar),
            // ],
            'sender'   => $this->sender->getChatProfile(),
            'receiver' => $receiver->getChatProfile(),
        ];
    }
}
