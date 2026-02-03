<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDeliveryAttachment extends Model
{
    protected $fillable = [
        'request_delivery_id',
        'attachment_path',
    ];

    public function delivery()
    {
        return $this->belongsTo(RequestDelivery::class, 'request_delivery_id');
    }
}
