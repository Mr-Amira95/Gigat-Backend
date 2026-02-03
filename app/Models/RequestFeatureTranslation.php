<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFeatureTranslation extends Model
{
    protected $fillable = [
        'request_feature_id',
        'language',
        'title'
    ];
}
