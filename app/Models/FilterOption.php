<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterOption extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'filter_id'
    ];
    protected $with = ['translation'];      

    /**
     * Get the filter that owns the option.
     */
    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }

    /**
     * Get the translations for the filter option.
     */
}
