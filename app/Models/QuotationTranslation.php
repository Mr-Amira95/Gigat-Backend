<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTranslation extends Model
{
    protected $fillable = ['quotation_id', 'language', 'title', 'description'];
}
