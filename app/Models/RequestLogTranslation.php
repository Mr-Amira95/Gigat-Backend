<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLogTranslation extends Model
{
    protected $fillable = ['request_log_id', 'language', 'action'];
}
