<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'attachment_url',
        'attachment_type',
        'is_read',
    ];

    // العلاقة مع الشات
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    // العلاقة مع المرسل
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }


    public function getAttachmentUrlAttribute($value)
    {
        return $value
            ? url($value)
            : null;
    }
}
