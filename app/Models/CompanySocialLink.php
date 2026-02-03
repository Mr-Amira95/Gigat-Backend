<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class CompanySocialLink extends Model
{
    use HasTranslations;

    protected $fillable = [
        'company_id',
        'icon',
        'url',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(CompanySocialLinkTranslation::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
