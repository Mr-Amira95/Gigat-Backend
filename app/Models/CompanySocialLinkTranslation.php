<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySocialLinkTranslation extends Model
{
    protected $fillable = [
        'company_social_link_id',
        'language',
        'title',
    ];
}
