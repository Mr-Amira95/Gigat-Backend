<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseTranslation extends Model
{
     protected $fillable = [
        'release_id',
        'language',
        'release_note',
    ];
}
