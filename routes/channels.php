<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
//     return \App\Models\Conversation::where('id', $conversationId)
//         ->where(function ($query) use ($user) {
//             $query->where('initiator_id', $user->id)
//                 ->orWhere('receiver_id', $user->id);
//         })->exists();
// });

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return true;
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
