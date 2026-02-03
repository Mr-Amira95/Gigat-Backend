<?php

namespace App\Repositories\Interfaces;

interface TicketRepositoryInterface
{
    public function getAllTickets();
    public function getUserTickets($userId);
    public function getTicketById($id);
    public function createTicket(array $data);
    public function addMessage($ticketId, array $messageData, $sender);
    public function addAttachment($messageId, array $attachmentData);
    public function closeTicket($ticket);
}
