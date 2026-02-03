<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConversationService extends Model
{
    use HasFactory;


    protected $fillable = [
        'ai_conversation_id',
        'service_id',
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
