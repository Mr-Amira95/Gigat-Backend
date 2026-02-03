<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Utilities\TranslationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profession extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;


    protected $fillable = [
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime'
    ];
    protected $with = ['translation'];


    public function users()
    {
        return $this->hasMany(User::class);
    }
    // public function title()
    // {
    //     return $this->hasOne(ProfessionTranslation::class)->where('language', app()->getLocale());
    // }

    public function translation()
    {
        return $this->hasOne(ProfessionTranslation::class)->where('language', app()->getLocale());
    }
}
