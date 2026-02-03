<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    use HasTranslations;

    protected $fillable = [
        'request_id',
        'user_id',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(RequestLogTranslation::class);
    }
    /**
     * Get the request that owns the log
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the user that created the log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequestLogAttachment::class, 'log_id');
    }
}
