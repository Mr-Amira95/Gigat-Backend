<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFeedbackAttachment extends Model
{
    protected $fillable = ['request_feedback_id', 'attachment_path'];

    public function feedback()
    {
        return $this->belongsTo(RequestFeedback::class, 'request_feedback_id');
    }
}
