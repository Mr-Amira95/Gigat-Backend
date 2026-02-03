<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class RequestDelivery extends Model
{
    use HasTranslations;

    protected $fillable = [
        'request_id',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(RequestDeliveryTranslation::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequestDeliveryAttachment::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
