<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerId extends Model
{
    protected $fillable = [
        'user_id',
        'player_id',
        'platform',
        'is_notifiable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
