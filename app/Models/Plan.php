<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    protected $with = ['translation'];


    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'plan_features')
            ->withPivot('type', 'value')
            ->withTimestamps()
            ->distinct();
    }
}
