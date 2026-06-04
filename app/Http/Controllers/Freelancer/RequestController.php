<?php

namespace App\Http\Controllers\Freelancer;

use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Request as ModelsRequest;
use App\Models\RequestLog;
use App\Models\RequestLogAttachment;
use App\Services\RequestService;
use App\Utilities\FileManager;
use App\Utilities\GoogleTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    protected $requestService;
    protected $googleTranslator;


    public function __construct(RequestService $requestService, GoogleTranslator $googleTranslator)
    {
        $this->requestService = $requestService;
        $this->googleTranslator = $googleTranslator;
    }

    public function index(Request $request)
    {
        $requests = ModelsRequest::with(['service.user', 'user'])
            ->whereHas('service', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->when($request->query('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('pages-freelancer.requests.index', compact('requests'));
    }


    public function show($id)
    {
        $request = $this->requestService->getRequestDetails($id);
        return view('pages-freelancer.requests.edit', compact('request'));
    }


    // public function changeStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:pending,in_progress,completed',
    //     ]);

    //     $requestItem = ModelsRequest::findOrFail($id);
    //     $requestItem->status = $request->status;
    //     $requestItem->save();

    //     // Save comment to request_logs
    //     $log = new RequestLog();
    //     $log->request_id = $requestItem->id;
    //     $log->action = auth()->user()->username . ' has updated the request status to ' . $request->status;
    //     $log->user_id = auth()->id();
    //     $log->save();

    //     return back()->with('success', 'Status updated successfully!');
    // }


    /**
     * Allowed status transitions — prevents illegal state changes (e.g. re-opening a cancelled request).
     * Key = current status, Value = statuses the freelancer may transition TO from that state.
     */
    private const ALLOWED_TRANSITIONS = [
        'pending'     => ['in_progress'],
        'in_progress' => ['completed'],
        'completed'   => ['in_progress'],
        // confirmed, cancelled, approved: managed by admin/API only — no freelancer transitions allowed
    ];

    public function changeStatus(Request $request, FileManager $fileManager)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'comment' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $requestItem = ModelsRequest::findOrFail($request->id ?? $request->route('id'));

        // P2-09/FUNC-02: Enforce state machine — reject illegal transitions
        $currentStatus = $requestItem->status;
        $newStatus     = $request->status;
        $allowed       = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            return back()->withErrors([
                'status' => __('Invalid status transition from :current to :new.', [
                    'current' => $currentStatus,
                    'new'     => $newStatus,
                ])
            ]);
        }

        $requestItem->status = $newStatus;
        $requestItem->save();

        // Save comment to request_logs
        // $log = new RequestLog();
        // $log->request_id = $requestItem->id;
        // $log->action = auth()->user()->username . ' has updated the request status to ' . $request->status . '. Comment: ' . $request->comment;
        // $log->user_id = auth()->id();
        // $log->save();


        // 1. Basic status update log (without comment)
        $logStatus = new RequestLog();
        $logStatus->request_id = $requestItem->id;
        $logStatus->user_id = auth()->id();
        $logStatus->save();

        $statusAction = auth()->user()->username . ' has updated the request status to ' . RequestStatusEnum::from($request->status)->label() . '.';
        foreach (['en', 'ar'] as $lang) {
            $logStatus->translations()->create(['language' => $lang, 'action' => $statusAction]);
        }
        \App\Jobs\TranslateEntityJob::dispatch(RequestLog::class, $logStatus->id, $statusAction, 'action');

        // 2. Comment log
        $log = new RequestLog();
        $log->request_id = $requestItem->id;
        $log->user_id = auth()->id();
        $log->save();

        foreach (['en', 'ar'] as $lang) {
            $log->translations()->create(['language' => $lang, 'action' => $request->comment]);
        }
        \App\Jobs\TranslateEntityJob::dispatch(RequestLog::class, $log->id, $request->comment, 'action');

        // Handle file attachment if uploaded (استخدام FileManager لتحميل الملف بنفس الطريقة)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $fileManager->upload('attachment', $file);

            // P2-04: use extension() (Fileinfo-based) instead of getClientOriginalExtension()
            $extension = strtolower($file->extension());

            $mediaType = match (true) {
                in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'image',
                in_array($extension, ['mp4', 'avi', 'mov', 'mkv'])          => 'video',
                default                                                     => 'file',
            };

            $attachment = new RequestLogAttachment();
            $attachment->log_id = $log->id;
            $attachment->media_path = $filename;
            $attachment->media_type = $mediaType;
            $attachment->save();
        }

        return back()->with('success', 'Status updated successfully with comment!');
    }




    public function addUpdate(Request $request, $id, FileManager $fileManager)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // max 5MB
        ]);

        $requestItem = ModelsRequest::findOrFail($id);

        // Save comment to request_logs
        $log = new RequestLog();
        $log->request_id = $requestItem->id;
        $log->user_id = auth()->id();
        $log->save();


        // Store placeholder translations immediately; async job handles translation
        foreach (['en', 'ar'] as $lang) {
            $log->translations()->create(['language' => $lang, 'action' => $request->comment]);
        }
        \App\Jobs\TranslateEntityJob::dispatch(RequestLog::class, $log->id, $request->comment, 'action');

        // Handle file attachment if uploaded
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $fileManager->upload('attachment', $file);

            $extension = strtolower($file->extension());

            $mediaType = match (true) {
                in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'image',
                in_array($extension, ['mp4', 'avi', 'mov', 'mkv'])          => 'video',
                default                                                     => 'file', // fallback for pdf, doc, etc
            };
            $attachment = new RequestLogAttachment();
            $attachment->log_id = $log->id;
            $attachment->media_path = $filename;
            $attachment->media_type = $mediaType;
            $attachment->save();
        }

        return back()->with('success', 'Update added successfully!');
    }


    public function logs($id)
    {
        $request = ModelsRequest::with(['logs.attachments'])->findOrFail($id);
        $request->logs = $request->logs->sortByDesc('id')->values();
        return view('pages-freelancer.requests.logs', compact('request'));
    }

    public function downloadContract($id)
    {
        $request = ModelsRequest::findOrFail($id);
        if (!$request->contract_path) {
            return redirect()->back()->with('error', 'Contract not found.');
        }

        // Build the correct absolute path
        $filePath = public_path($request->contract_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Contract file not found.');
        }

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
