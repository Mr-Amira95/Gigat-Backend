<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Request as ModelsRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Services\TicketService;
use App\Utilities\FileManager;
use App\Utilities\GenerateCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index()
    {
        $tickets = $this->ticketService->getUserTickets(auth()->id());
        return view('pages-freelancer.tickets.index', compact('tickets'));
    }
    public function show($id)
    {
        $ticket = $this->ticketService->getTicketById($id);
        return view('pages-freelancer.tickets.show', compact('ticket'));
    }
    public function create(Request $request)
    {
        $requests = ModelsRequest::with(['service.user', 'user'])
            ->whereHas('service', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->get();

        return view('pages-freelancer.tickets.create', compact('requests'));
    }


    public function store(Request $request, FileManager $fileManager)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'request_id' => 'nullable|exists:requests,id',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        $user = Auth::user();

        $ticketData = [
            'user_id' => $user->id,
            'subject' => $request->subject,
            'request_id' => $request->request_id,
            'status' => 'open',
            'code' => GenerateCode::generateTicketCode(),
        ];

        $ticket = Ticket::create($ticketData);

        // Handle message
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $fileManager->upload('attachemnt', $request->file('attachment'));
            $fileType = $file->getClientOriginalExtension(); // or getClientMimeType()

        }


        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'messageable_type' => get_class($user),
            'messageable_id' => $user->id,
        ]);

        if (!empty($filename)) {
            TicketAttachment::create([
                'message_id' => $message->id,
                'user_id' => $user->id,
                'file_path' => $filename,
                'file_type'  => $fileType,
            ]);
        }
        return redirect()->route('freelancer.tickets.index')
            ->with('success', __('ticket_created_successfully'));
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

        return redirect()->route('freelancer.tickets.show', $ticket->id)
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
