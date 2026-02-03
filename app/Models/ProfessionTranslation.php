<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'profession_id',
        'language',
        'language_id',
        'title'
    ];

    /**
     * Get the profession that owns the translation.
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get the language associated with this translation.
     */
    public function languageModel()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
