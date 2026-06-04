<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'stripe_session_id',
        'request_id',
        'amount',
        'fees',
        'commission',
        'discount',
        'total',
        'client_payment_status',
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
