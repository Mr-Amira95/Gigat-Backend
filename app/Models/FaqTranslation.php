<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_id',
        'language',
        'question',
        'answer',
        'media_path',
        'media_type'
    ];

    /**
     * Get the FAQ that owns the translation.
     */
    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }
}
