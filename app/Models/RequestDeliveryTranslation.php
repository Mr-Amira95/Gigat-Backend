<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDeliveryTranslation extends Model
{
    protected $fillable = [
        'request_delivery_id',
        'language',
        'message',
    ];
}
