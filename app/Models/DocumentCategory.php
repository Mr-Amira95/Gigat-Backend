<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use SoftDeletes, HasTranslations;


    protected $fillable = [
        'parent_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(DocumentCategoryTranslation::class);
    }

    /**
     * Get the parent document category.
     */
    public function parent()
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id');
    }

    /**
     * Get the child document categories.
     */
    public function children()
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id');
    }

    public function documents()
    {
        return $this->hasMany(DocumentContent::class, 'document_category_id');
    }
}
