<?php

namespace App\Utilities;

use App\Models\Ticket;

class GenerateCode
{
    public static function generate(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    public static function generateTicketCode()
    {
        $lastTicket = Ticket::latest('id')->first();
        $nextId = $lastTicket ? $lastTicket->id + 1 : 1;

        return 'TCKT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }
}
