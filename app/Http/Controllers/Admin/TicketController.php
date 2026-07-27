<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewSupportTicketMail;
use App\Models\Notification;
use App\Models\Portfolio;
use App\Models\PlayerId;
use App\Models\Request as ModelsRequest;
use App\Models\Service;
use App\Models\User;
use App\Services\OneSignalService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

    public function create()
    {
        return view('pages.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'subject'      => 'required|string|max:255',
            'priority'     => 'nullable|in:low,medium,high,urgent',
            'link_type'    => 'nullable|in:request,service,portfolio',
            'link_id'      => 'nullable|integer',
            'message'      => 'required|string',
            'attachment'   => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,docx',
        ]);

        $targetUser = User::findOrFail($data['user_id']);

        $linkColumn = match ($data['link_type'] ?? null) {
            'request'   => 'request_id',
            'service'   => 'service_id',
            'portfolio' => 'portfolio_id',
            default     => null,
        };
        if ($linkColumn && !empty($data['link_id'])) {
            $data[$linkColumn] = $data['link_id'];
        }
        $data['attachment'] = $request->file('attachment');

        $ticket = $this->ticketService->createTicketByAdmin($data, $targetUser, auth('admin')->user());

        Mail::to($targetUser->email)->queue(new NewSupportTicketMail($ticket));

        $this->noticeService->send(
            $targetUser->id,
            [
                'en' => __('new_support_ticket_title', [], 'en'),
                'ar' => __('new_support_ticket_title', [], 'ar'),
            ],
            [
                'en' => __('new_support_ticket_message', [], 'en'),
                'ar' => __('new_support_ticket_message', [], 'ar'),
            ],
            'support',
            $ticket->id,
            true
        );

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', __('ticket_created_successfully'));
    }

    public function searchUsers(Request $request)
    {
        $q = $request->get('q', '');
        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'username', 'email']);

        return response()->json(['status' => true, 'data' => $users->map(fn ($u) => [
            'id'   => $u->id,
            'text' => "{$u->username} ({$u->email})",
        ])]);
    }

    public function linkOptions(Request $request, $userId)
    {
        $type = $request->get('type');

        $items = match ($type) {
            'request' => ModelsRequest::where('user_id', $userId)
                ->orWhereHas('service', fn ($q) => $q->where('user_id', $userId))
                ->get()
                ->map(fn ($r) => ['id' => $r->id, 'text' => $r->order_number]),
            'service' => Service::where('user_id', $userId)
                ->get()
                ->map(fn ($s) => ['id' => $s->id, 'text' => $s->translation->title ?? ('#' . $s->id)]),
            'portfolio' => Portfolio::where('user_id', $userId)
                ->get()
                ->map(fn ($p) => ['id' => $p->id, 'text' => $p->translation->title ?? ('#' . $p->id)]),
            default => collect(),
        };

        return response()->json(['status' => true, 'data' => $items->values()]);
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
