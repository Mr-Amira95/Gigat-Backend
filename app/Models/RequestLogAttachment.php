<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLogAttachment extends Model
{
    protected $fillable = ['media_path','media_type','log_id'];

    
}
