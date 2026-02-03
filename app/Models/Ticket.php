<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['code','user_id', 'request_id', 'subject', 'status', 'priority', 'assigned_to'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function request()   
    {
        return $this->belongsTo(Request::class);
    }
    public function messages()
    {
        
        return $this->hasMany(TicketMessage::class)->orderBy('id', 'desc');
    }
}
