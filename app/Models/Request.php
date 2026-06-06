<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasTranslations;

    protected $fillable = [
        'order_number',
        'user_id',
        'service_id',
        'plan_id',
        'status',
        'image',
        'start_date',
        'end_date',
        'need_action',
        'contract_path',
        'revisions_count',
    ];

    protected $with = ['translation'];

    protected $casts = [
        'revisions_count' => 'integer',
        'user_id'         => 'integer',
        'service_id'      => 'integer',
        'plan_id'         => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(RequestTranslation::class);
    }
    /**
     * Get the user that owns the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service associated with the request
     */
    // public function service(): BelongsTo
    // {
    //     return $this->belongsTo(Service::class);
    // }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }


    /**
     * Get the plan associated with the request
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the logs for the request
     */
    public function logs(): HasMany
    {
        return $this->hasMany(RequestLog::class);
    }

    public function features()
    {
        return $this->hasMany(RequestFeature::class);
    }

    public function deliveries()
    {
        return $this->hasMany(RequestDelivery::class)->orderBy('created_at', 'desc');
    }

    public function feedbacks()
    {
        return $this->hasMany(RequestFeedback::class)->orderBy('created_at', 'desc');
    }

    public function finance()
    {
        return $this->hasOne(Finance::class);
    }

    public function ratings()
    {
        return $this->hasOne(UserRating::class);
    }
}
