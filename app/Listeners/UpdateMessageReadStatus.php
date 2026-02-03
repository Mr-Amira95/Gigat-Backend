<?php

namespace App\Listeners;

use App\Events\MessageReadEvent;
use App\Models\Message;

class UpdateMessageReadStatus
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\MessageReadEvent  $event
     * @return void
     */
    public function handle(MessageReadEvent $event)
    {
        $messages = Message::where('conversation_id', $event->conversationId)
            ->where('sender_id','!=', $event->userId)
            ->where('is_read', false)
            ->get();

        if ($messages->isNotEmpty()) {
            Message::where('conversation_id', $event->conversationId)
                ->where('sender_id', '!=', $event->userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }
}
