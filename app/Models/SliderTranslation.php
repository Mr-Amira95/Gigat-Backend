<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'slider_id',
        'language',
        'title',
        'description',
        'button_text',
        'media_path',
        'media_type'
    ];

    /**
     * Get the slider that owns the translation.
     */
    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
