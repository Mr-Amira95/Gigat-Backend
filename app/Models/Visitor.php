<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    protected $table = 'analytics_visitors';

    protected $fillable = [
        'visitor_uuid',
        'platform',
        'device_type',
        'device_os',
        'device_browser',
        'device_model',
        'country',
        'user_id',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }
}
