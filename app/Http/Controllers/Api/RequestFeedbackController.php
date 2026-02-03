<?php

namespace App\Http\Controllers\Api;

use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestFeedbackRequest;
use App\Http\Resources\RequestFeedbackResource;
use App\Services\RequestFeedbackService;
use App\Services\RequestService;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Traits\BaseResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestFeedbackController extends Controller
{
    use BaseResponse;

    protected $feedbackService;
    protected $requestService;

    public function __construct(RequestFeedbackService $feedbackService, RequestService $requestService)
    {
        $this->feedbackService = $feedbackService;
        $this->requestService = $requestService;
    }

    public function store(RequestFeedbackRequest $request, $id)
    {
        try {

            // 1. Fetch request
            $req = RequestModel::findOrFail($id);

            // 2. Ensure request is completed
            if ($req->status !== RequestStatusEnum::COMPLETED->value) {
                return $this->errorResponse(__('request_feedback_not_allowed'));
            }


            $isRejection = $request->status !== 'confirmed';
            // Get revision limit from plan "features"
            $revisionLimit = $req->features
                ->where('type', 'revisions')
                ->first()
                ->value ?? 0;

            if ($isRejection && $req->revisions_count >= $revisionLimit) {
                return $this->errorResponse(__('max_revisions_reached'));
            }

            $newStatus = $request->status === 'confirmed'
                ? RequestStatusEnum::CONFIRMED->value
                : RequestStatusEnum::IN_PROGRESS->value;

            if ($newStatus == RequestStatusEnum::IN_PROGRESS->value) {
                $req->increment('revisions_count');
            }

            // 4. Save LOG entry with attachments
            $logData = [
                'user_id'     => Auth::id(),
                'request_id'  => $id,
                'status'      => $newStatus,
                'action'      => $request->message,
                'attachments' => $request->file('attachments', []),
            ];
            $this->requestService->addComment($logData);

            // 5. Save feedback (message + attachments)
            $feedbackData = [
                'request_id'  => $id,
                'message'     => $request->message,
                'attachments' => $request->file('attachments', []),
            ];

            $feedback = $this->feedbackService->create($feedbackData);

            return $this->successResponse(
                __('feedback_submitted_successfully'),
                new RequestFeedbackResource($feedback)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
