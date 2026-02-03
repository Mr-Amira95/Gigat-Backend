<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioMedia extends Model
{
    protected $table = 'portfolio_media';

    protected $fillable = [
        'portfolio_id',
        'media_path',
        'media_type',
        'is_cover'
    ];
    protected $casts = [
        'is_cover'=>'boolean'
    ];
    /**
     * Get the portfolio that owns the media.
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
