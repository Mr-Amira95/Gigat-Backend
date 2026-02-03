<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMedia extends Model
{
    use HasFactory;

    protected $table = 'service_media';

    protected $fillable = [
        'service_id',
        'media_path',
        'media_type',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /**
     * Get the service that owns the media.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
