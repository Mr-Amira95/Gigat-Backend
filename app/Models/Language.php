<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'flag',
        'is_active'
    ];

    /**
     * Get the freelancer languages for this language.
     */
    public function UserLanguages()
    {
        return $this->hasMany(UserLanguage::class);
    }
}
