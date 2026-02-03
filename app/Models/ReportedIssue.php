<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportedIssue extends Model
{

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
