<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentContentTranslation extends Model
{
    protected $fillable = [
        'document_content_id',
        'language',
        'content',
        'title',
    ];
}
