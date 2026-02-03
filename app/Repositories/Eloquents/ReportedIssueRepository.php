<?php

namespace App\Repositories\Eloquents;

use App\Models\ReportedIssue;
use App\Repositories\Interfaces\ReportedIssueRepositoryInterface;

class ReportedIssueRepository implements ReportedIssueRepositoryInterface
{

    public function create(array $data)
    {
        return ReportedIssue::create($data);
    }

    public function index($status = null)
    {
        return ReportedIssue::with('user')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get();
    }

    public function updateStatus($id, string $status)
    {
        $issue = ReportedIssue::findOrFail($id);
        $issue->update(['status' => $status]);
        return $issue;
    }
}
