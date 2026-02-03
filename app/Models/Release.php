<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Release extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
        'android_version',
        'ios_version',
        'web_version',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];
    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(ReleaseTranslation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
