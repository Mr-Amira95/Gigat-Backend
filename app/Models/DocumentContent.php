<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentContent extends Model
{
    use SoftDeletes, HasTranslations;


    protected $table = 'document_content';
    
    protected $fillable = [
        'document_category_id',
    ];

    protected $casts = [
        'document_category_id' => 'integer',
    ];

    protected $with = ['translation'];


    /**
     * Get all translations
     */
    public function translations()
    {
        return $this->hasMany(DocumentContentTranslation::class);
    }

    /**
     * Get category
     */
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }
}
