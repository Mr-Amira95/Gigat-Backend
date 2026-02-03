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


    public function changeStatus(Request $request, FileManager $fileManager)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'comment' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // max 5MB
        ]);

        $requestItem = ModelsRequest::findOrFail($request->id ?? $request->route('id'));
        $requestItem->status = $request->status;
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
        foreach ($this->googleTranslator->translateForStorage($statusAction) as $lang => $text) {
            $logStatus->translations()->create([
                'language' => $lang,
                'action'   => $text,
            ]);
        }

        // 2. Comment log
        $log = new RequestLog();
        $log->request_id = $requestItem->id;
        $log->user_id = auth()->id();
        $log->save();

        foreach ($this->googleTranslator->translateForStorage($request->comment) as $lang => $text) {
            $log->translations()->create([
                'language' => $lang,
                'action'   => $text,
            ]);
        }

        // Handle file attachment if uploaded (استخدام FileManager لتحميل الملف بنفس الطريقة)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $fileManager->upload('attachment', $file);

            $extension = strtolower($file->getClientOriginalExtension());

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


        // Translate the comment into en/ar and save translations
        foreach ($this->googleTranslator->translateForStorage($request->comment) as $lang => $text) {
            $log->translations()->create([
                'language' => $lang,
                'action'   => $text,
            ]);
        }

        // Handle file attachment if uploaded
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $fileManager->upload('attachemnt', $request->file('attachment'));
            $fileType = $file->getClientOriginalExtension(); // or getClientMimeType()

            $extension = strtolower($file->getClientOriginalExtension());

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
        // dd($request);
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
