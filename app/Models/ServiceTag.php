<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'tag_id',
    ];

    /**
     * Get the service that owns the tag.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the tag associated with the service.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
} 