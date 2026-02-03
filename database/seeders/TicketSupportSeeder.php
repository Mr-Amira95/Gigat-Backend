<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Database\Seeder;
use App\Models\TicketAttachment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TicketSupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // Example user

        // Create a ticket
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Issue with my website',
            'status' => 'open',
            'priority' => 'high',
            'assigned_to' => null
        ]);

       $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'My website is down. Please help!',
            'is_admin' => false
        ]);

        TicketAttachment::create([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'file_path' => 'uploads/screenshots/error.png',
            'file_type' => 'image'
        ]);
    }
}
