<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFeedbackTranslation extends Model
{
    protected $fillable = ['request_feedback_id', 'language', 'message'];
}
