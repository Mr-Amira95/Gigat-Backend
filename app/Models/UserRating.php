<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    protected $fillable = [
        'freelancer_id',
        'client_id',
        'request_id',
        'rating',
        'review'
    ];

    protected $casts = [
        'freelancer_id' => 'integer',
        'client_id'     => 'integer',
        'request_id'    => 'integer',
    ];

    public function rater()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}