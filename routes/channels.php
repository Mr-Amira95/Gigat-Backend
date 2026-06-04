<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
//     return \App\Models\Conversation::where('id', $conversationId)
//         ->where(function ($query) use ($user) {
//             $query->where('initiator_id', $user->id)
//                 ->orWhere('receiver_id', $user->id);
//         })->exists();
// });

// SEC-09: Use private channel + verify the requesting user is a participant
// (Broadcast::routes already uses auth:freelancer so $user is the authenticated User)
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return Chat::where('id', $chatId)
        ->where(function ($query) use ($user) {
            $query->where('user_id_one', $user->id)
                  ->orWhere('user_id_two', $user->id);
        })->exists();
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
