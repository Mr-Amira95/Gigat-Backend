<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AiConversationService extends Pivot
{
    protected $fillable = [
        'ai_conversation_id',
        'service_id',
    ];

    protected $casts = [
        'ai_conversation_id' => 'integer',
        'service_id'         => 'integer',
    ];


    public function services()
    {
        return $this->belongsToMany(Service::class, 'ai_conversation_service');
    }


    public function aiConversations()
    {
        return $this->belongsToMany(AiConversation::class, 'ai_conversation_service');
    }
}
