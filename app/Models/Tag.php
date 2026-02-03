<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'sub_category_id',
        'slug',
    ];
    protected $with = ['translation'];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_tags')
            ->withTimestamps();
    }
}
