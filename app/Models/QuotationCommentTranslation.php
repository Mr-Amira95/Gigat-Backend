<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationCommentTranslation extends Model
{
    protected $fillable = ['quotation_comment_id', 'language', 'comment'];
}
