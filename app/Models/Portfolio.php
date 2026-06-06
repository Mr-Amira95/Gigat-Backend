<?php

namespace App\Models;

use App\Traits\BlockFilterTrait;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes, BlockFilterTrait, HasTranslations;

    protected $fillable = [
        'user_id',
        'is_featured'
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(PortfolioTranslation::class);
    }
    /**
     * Get the user that owns the portfolio.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media for the portfolio.
     */
    public function media(): HasMany
    {
        return $this->hasMany(PortfolioMedia::class);
    }
    public function services()
    {
        return $this->belongsToMany(Service::class, 'portfolio_services');
    }
}
