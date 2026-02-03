<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTranslation extends Model
{
    protected $fillable = [
        'company_id',
        'language',
        'name',
        'description',
        'country_of_registration',

    ];
}
