<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'request_id',
        'amount',
        'fees',
        'commission',
        'discount',
        'total',
        'payment_status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'paid_at'        => 'datetime',
    ];
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
