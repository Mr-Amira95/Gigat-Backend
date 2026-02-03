<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class FixedFeature extends Model
{
    protected $fillable = ['plan_id', 'title_en', 'title_ar'];
    protected $appends = ['title'];
    public function getTitleAttribute()
    {
        $locale = App::getLocale();

        return $this->{"title_{$locale}"};
    }
    public function plan(){
        return $this->belongsTo(Plan::class);
    }
}
