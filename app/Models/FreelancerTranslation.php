<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerTranslation extends Model
{
    protected $fillable = ['freelancer_id', 'language', 'bio'];
}
