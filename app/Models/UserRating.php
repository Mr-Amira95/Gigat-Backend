<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    protected $fillable = [
        'rater_id',
        'ratee_id',
        'request_id',
        'rating',
        'review'
    ];

    protected $casts = [
        'rater_id'  => 'integer',
        'ratee_id'  => 'integer',
        'request_id' => 'integer',
    ];

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}