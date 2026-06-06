<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class RequestFeature extends Model
{
    use HasTranslations;

    protected $fillable = [
        'plan_id',
        'request_id',
        'type',
        'value'
    ];

    protected $casts = [
        'plan_id'    => 'integer',
        'request_id' => 'integer',
    ];
    protected $with = ['translation'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function translations()
    {
        return $this->hasMany(RequestFeatureTranslation::class);
    }
}
