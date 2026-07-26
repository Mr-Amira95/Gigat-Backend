<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['code','user_id', 'request_id', 'service_id', 'portfolio_id', 'subject', 'status', 'priority', 'assigned_to'];

    protected $casts = [
        'user_id'      => 'integer',
        'request_id'   => 'integer',
        'service_id'   => 'integer',
        'portfolio_id' => 'integer',
        'assigned_to'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
    public function assignedAdmin()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }
    public function messages()
    {
        
        return $this->hasMany(TicketMessage::class)->orderBy('id', 'desc');
    }
}
