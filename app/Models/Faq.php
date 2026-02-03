<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = [
        'faqable_id',
        'faqable_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
    protected $with = ['translation'];
    /**
     * Get the parent faqable model.
     */
    public function faqable()
    {
        return $this->morphTo();
    }
}
