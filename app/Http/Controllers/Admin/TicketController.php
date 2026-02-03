<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Services\OneSignalService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Ticket;
use App\Services\NoticeService;

class TicketController extends Controller
{
    protected $ticketService;
    protected $noticeService;


    public function __construct(TicketService $ticketService, NoticeService $noticeService)
    {
        $this->ticketService = $ticketService;
        $this->noticeService    = $noticeService;
    }

    public function index()
    {
        $tickets = $this->ticketService->getAllTickets();
        return view('pages.tickets.index', compact('tickets'));
    }
    public function show($id)
    {
        $ticket = $this->ticketService->getTicketById($id);
        return view('pages.tickets.show', compact('ticket'));
    }
    public function reply(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,docx',
        ]);
        $ticket = $this->ticketService->getTicketById($ticketId);


        $this->ticketService->addMessage([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'attachment' => $request->file('attachment'),
        ], auth()->user());

        // one signal notification
        $user = $ticket->user;
        if ($user) {
            $titles = [
                'en' => __('support_ticket_update_title', [], 'en'),
                'ar' => __('support_ticket_update_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('support_ticket_update_message', [], 'en'),
                'ar' => __('support_ticket_update_message', [], 'ar'),
            ];

            $this->noticeService->send(
                $user->id,
                $titles,
                $messages,
                'support',
                $ticket->id,
                true // 👈 broadcast unread count, since ticket updates usually require user action
            );
        }
        // *********************************************//



        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', __('Reply Sent Successfully'));
    }

    public function changeStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', Rule::in(['open', 'closed'])],
        ]);

        $ticket->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Ticket status updated successfully.');
    }
}
