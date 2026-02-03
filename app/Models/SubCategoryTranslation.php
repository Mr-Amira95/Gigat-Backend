<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_category_id',
        'language',
        'title'
    ];

    /**
     * Get the sub-category that owns the translation.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
