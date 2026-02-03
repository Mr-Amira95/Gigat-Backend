<?php

namespace App\Repositories\Eloquents;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Repositories\Interfaces\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    public function getAllTickets()
    {
        return Ticket::with(['user', 'messages.messageable', 'messages.attachments'])->latest()->get();
    }

    public function getTicketById($id)
    {
        // dd($id);
        // return Ticket::with(['user'])->findOrFail($id);
        return Ticket::with(['user', 'messages.messageable', 'messages.attachments'])->findOrFail($id);
    }

    public function createTicket(array $data)
    {
        return Ticket::create($data);
    }

    public function addMessage($ticketId, array $messageData, $sender)
    {
        return $sender->ticketMessages()->create([
            'ticket_id' => $ticketId,
            'message' => $messageData['message'],
            'is_admin' => $sender instanceof \App\Models\Admin
        ]);
    }

    public function addAttachment($messageId, array $attachmentData)
    {
        $message = TicketMessage::findOrFail($messageId);

        if (isset($attachmentData[0])) {
            return $message->attachments()->createMany($attachmentData);
        }

        return $message->attachments()->create($attachmentData);
    }


    public function getUserTickets($userId)
    {
        return Ticket::where('user_id', $userId)->with('user', 'request.service')->latest()->get();
    }

    public function closeTicket($ticket)
    {
        return $ticket->update(['status' => 'closed']);
    }
}
