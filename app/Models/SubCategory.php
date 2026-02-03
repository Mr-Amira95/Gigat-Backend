<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'category_id',
        'icon',
        'is_active'
    ];
    protected $with = ['translation'];

    /**
     * Get the category that owns the sub-category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the services for the sub-category.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function tags(){
        return $this->hasMany(Tag::class);
    }

}
