<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'messageable_id', 'messageable_type', 'message', 'is_admin'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function messageable()
    {
        return $this->morphTo();
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id');
    }
}
