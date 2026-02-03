<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeatureTranslation extends Model
{
    protected $fillable = [
        'plan_feature_id',
        'language',
        'title'
    ];

    public function planFeature(): BelongsTo
    {
        return $this->belongsTo(PlanFeature::class);
    }
} 