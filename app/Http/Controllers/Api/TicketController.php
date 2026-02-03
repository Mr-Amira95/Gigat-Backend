<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TicketService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Requests\Api\TicketRequest;
use App\Http\Resources\TicketMessageResource;
use App\Http\Requests\Api\TicketMessageRequest;

class TicketController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index()
    {
        $tickets = $this->ticketService->getAllTickets();
        return $this->successResponse(__('success'),TicketResource::collection($tickets));
    }

    public function show($id)
    {
        $ticket = $this->ticketService->getTicketById($id);
        return $this->successResponse(__('success'), TicketMessageResource::collection($ticket->messages));
    }

    public function store(TicketRequest $request)
    {
        $ticket = $this->ticketService->createTicket($request->validated(), auth()->guard('api')->user());
        return $this->successResponse(__('success'), new TicketResource($ticket));
    }

    public function addMessage(TicketMessageRequest $request)
    {
        $message = $this->ticketService->addMessage($request->validated(), auth()->guard('api')->user());
        return $this->successResponse(__('success'), new TicketMessageResource($message));
    }

    public function userTickets()
    {
        $tickets = $this->ticketService->getUserTickets(auth()->guard('api')->id());
        return $this->successResponse(__('success'), TicketResource::collection($tickets));
    }
    public function closeTicket($ticketId)
    {
        $this->ticketService->closeTicket($ticketId);
        return $this->successResponse(__('success'));
    }
}
