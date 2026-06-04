<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AiConversationService extends Pivot
{
    protected $fillable = [
        'ai_conversation_id',
        'service_id',
    ];
}
