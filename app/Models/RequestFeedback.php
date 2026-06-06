<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class RequestFeedback extends Model
{
    use HasTranslations;

    protected $table = 'request_feedbacks';

    protected $fillable = ['request_id'];

    protected $casts = [
        'request_id' => 'integer',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(RequestFeedbackTranslation::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequestFeedbackAttachment::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
