<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'blocker_type',
        'blocked_type'
    ];

    protected $casts = [
        'blocker_id' => 'integer',
        'blocked_id' => 'integer',
    ];


    public function blockedUser()
{
    return $this->belongsTo(User::class, 'blocked_id');
}

}
