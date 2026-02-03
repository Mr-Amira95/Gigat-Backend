<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportedIssue;
use App\Services\ReportedIssueService;
use Illuminate\Http\Request;

class ReportedIssueController extends Controller
{
    protected ReportedIssueService $service;

    public function __construct(ReportedIssueService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $issues = $this->service->index($status);

        return view('pages.reported-issues.index', compact('issues', 'status'));
    }

    public function updateStatus(ReportedIssue $issue, $status)
    {
        $this->service->updateStatus($issue->id, $status);

        $message = match ($status) {
            'pending'   => __('issue_marked_pending'),
            'resolved'  => __('issue_marked_resolved'),
            'cancelled' => __('issue_marked_cancelled'),
            default     => __('success'),
        };

        return back()->with('success', $message);
    }
}
