<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTranslation extends Model
{
    protected $fillable = [
        'request_id',
        'language',
        'title',
        'description',
    ];
}
