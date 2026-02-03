<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewTranslation extends Model
{
    protected $fillable = ['review_id', 'language', 'comment'];
}
