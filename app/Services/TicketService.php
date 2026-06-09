<?php

namespace App\Services;

use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Utilities\FileManager;
use App\Utilities\GenerateCode;

class TicketService
{
    protected TicketRepositoryInterface $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getAllTickets()
    {
        return $this->ticketRepository->getAllTickets();
    }

    public function getUserTickets($userId)
    {
        return $this->ticketRepository->getUserTickets($userId);
    }

    public function getTicketById($id)
    {
        return $this->ticketRepository->getTicketById($id);
    }

    public function createTicket(array $data, $user)
    {
        $data['user_id'] = $user->id;
        $data['status'] = 'open';
        $data['code'] = GenerateCode::generateTicketCode();
        $ticket = $this->ticketRepository->createTicket($data);

        // if (!empty($data['message'])) {
        //     $this->addMessage([
        //         'ticket_id' => $ticket->id,
        //         'user_id' => $user->id,
        //         'message' => $data['message'],
        //         'attachment' => $data['attachment'] ?? null
        //     ], $user);
        // }
        if (!empty($data['message']) || !empty($data['attachment'])) {
            $this->addMessage([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'message'   => $data['message'] ?? null,
                'attachment' => $data['attachment'] ?? null
            ], $user);
        }


        return $ticket->load(['messages.attachments']);
    }

    public function addMessage(array $data, $user)
    {
        if (!($user instanceof \App\Models\Admin)) {
            $this->ticketRepository->getTicketByIdForUser($data['ticket_id'], auth('api')->id());
        }

        $message = $this->ticketRepository->addMessage($data['ticket_id'], [
            'user_id' => $user->id,
            'message' => $data['message'],
            'is_admin' => false
        ], $user);

        if (!empty($data['attachment'])) {
            // Updated at 24-7-2025
            $userId = $user instanceof \App\Models\User ? $user->id : null;

            $this->handleAttachment($message->id, $data['attachment'], $userId);
        }

        return $message->load('attachments');
    }

    protected function handleAttachment($messageId, $files, $userId)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->uploadAndAttach($messageId, $file, $userId);
            }
        } else {
            $this->uploadAndAttach($messageId, $files, $userId);
        }
    }

    protected function uploadAndAttach($messageId, $file, $userId)
    {
        $path = FileManager::upload('tickets', $file);
        $this->ticketRepository->addAttachment($messageId, [
            'user_id' => $userId,
            'file_path' => $path,
            'file_type' => FileManager::getFileTypeFromPath($path)
        ]);
    }

    public function closeTicket($ticketId)
    {
        $ticket = $this->ticketRepository->getTicketByIdForUser($ticketId, auth('api')->id());
        return $this->ticketRepository->closeTicket($ticket);
    }
}
