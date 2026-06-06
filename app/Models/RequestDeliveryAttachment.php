<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDeliveryAttachment extends Model
{
    protected $fillable = [
        'request_delivery_id',
        'attachment_path',
    ];

    protected $casts = [
        'request_delivery_id' => 'integer',
    ];

    public function delivery()
    {
        return $this->belongsTo(RequestDelivery::class, 'request_delivery_id');
    }
}
