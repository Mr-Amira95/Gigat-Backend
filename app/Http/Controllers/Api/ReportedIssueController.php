<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportedIssueResource;
use App\Services\ReportedIssueService;
use Illuminate\Http\Request;

class ReportedIssueController extends Controller
{
    protected ReportedIssueService $service;

    public function __construct(ReportedIssueService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:service,portfolio,freelancer,general',
            'message' => 'required|string',
            'type_id' => 'required_unless:type,general|nullable|integer',
        ]);

        $issue = $this->service->create(
            $request->all(),
            auth('api')->check() ? auth('api')->id() : null
        );

        return $this->successResponse(__('success'), new ReportedIssueResource($issue));
    }
}
