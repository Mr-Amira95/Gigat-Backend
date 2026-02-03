<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotConversation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(BotMessage::class);
    }
}
