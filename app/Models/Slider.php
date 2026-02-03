<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'is_active',
        'button_action',
        'platform',
        'ordering'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $with = ['translation'];


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
