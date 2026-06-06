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
        'client_payment_status',
        'payment_status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'paid_at'    => 'datetime',
        'request_id' => 'integer',
    ];
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
