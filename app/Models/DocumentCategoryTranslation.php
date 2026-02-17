<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategoryTranslation extends Model
{
    protected $fillable = [
        'document_category_id',
        'name',
        'language',
    ];
}
