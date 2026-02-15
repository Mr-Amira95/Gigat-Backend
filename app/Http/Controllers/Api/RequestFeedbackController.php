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
use App\Services\NoticeService;
use App\Traits\BaseResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestFeedbackController extends Controller
{
    use BaseResponse;

    protected $feedbackService;
    protected $requestService;
    protected $noticeService;


    public function __construct(
        RequestFeedbackService $feedbackService,
        RequestService $requestService,
        NoticeService $noticeService
    ) {
        $this->feedbackService = $feedbackService;
        $this->requestService = $requestService;
        $this->noticeService  = $noticeService;
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

            // If confirmed → notify client
            if ($newStatus == RequestStatusEnum::CONFIRMED->value) {

                $orderNumber = $req->order_number ?? 'Unknown';

                $titles = [
                    'en' => __('messages.request_confirmed_title', [], 'en'),
                    'ar' => __('messages.request_confirmed_title', [], 'ar'),
                ];

                $messages = [
                    'en' => __('messages.request_confirmed_message', [
                        'order_number' => $orderNumber
                    ], 'en'),

                    'ar' => __('messages.request_confirmed_message', [
                        'order_number' => $orderNumber
                    ], 'ar'),
                ];

                $this->noticeService->send(
                    $req->user_id,
                    $titles,
                    $messages,
                    'request',
                    $req->id
                );
            }

            return $this->successResponse(
                __('feedback_submitted_successfully'),
                new RequestFeedbackResource($feedback)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
