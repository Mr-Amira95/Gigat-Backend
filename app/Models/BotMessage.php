<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotMessage extends Model
{
    public function conversation()
    {
        return $this->belongsTo(BotConversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isFromUser()
    {
        return $this->sender_type === 'user';
    }

    public function isFromBot()
    {
        return $this->sender_type === 'bot';
    }
}
