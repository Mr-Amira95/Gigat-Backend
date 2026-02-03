<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'category_id',
        'type',
        'min_value',
        'max_value'
    ];
    protected $with = ['translation'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function options()
    {
        return $this->hasMany(FilterOption::class);
    }
}
