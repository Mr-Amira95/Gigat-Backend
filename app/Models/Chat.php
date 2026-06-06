<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'user_id_one',
        'user_id_two',
        'user_one_flag',
        'user_two_flag',
        'user_one_deleted_at',
        'user_two_deleted_at',
    ];

    protected $casts = [
        'user_id_one' => 'integer',
        'user_id_two' => 'integer',
    ];

    // علاقة المستخدم الأول
    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_one');
    }

    // علاقة المستخدم الثاني
    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_two');
    }

    // جميع الرسائل ضمن هذا الشات
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }
}
