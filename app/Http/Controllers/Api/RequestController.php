<?php

namespace App\Http\Controllers\Api;

use App\Events\NewNotificationCount;
use App\Events\NewNotificationEvent;
use Illuminate\Http\Request;
use App\Services\RequestService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RequestResource;
use App\Http\Resources\RequestLogResource;
use App\Http\Resources\RequestCommentResource;
use App\Http\Resources\RequestDetailsResource;
use App\Http\Requests\Api\RequestCreateRequest;
use App\Http\Requests\Api\AddRequestCommentRequest;
use App\Mail\NewRequestClientMail;
use App\Mail\NewRequestFreelancerMail;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Notifications\NewPortalNotification;
use App\Services\ContractGeneratorService;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    protected $requestService;
    protected $contractGenerator;
    protected $noticeService;


    public function __construct(RequestService $requestService, ContractGeneratorService $contractGenerator, NoticeService $noticeService)
    {
        $this->requestService = $requestService;
        $this->contractGenerator = $contractGenerator;
        $this->noticeService    = $noticeService;
    }
    public function getByUser(Request $request)
    {
        try {
            $perPage = $request->query('per_page');
            $requests = $this->requestService->getByUser($perPage);
            return $this->successResponse(__('success'), [
                'requests' => RequestResource::collection($requests['data']),
                'meta' => $requests['meta']
            ]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function getByFreelancer(Request $request)
    {
        try {
            $perPage = $request->input('per_page');
            $requests = $this->requestService->getByFreelancer($perPage);
            return $this->successResponse(
                __('success'),
                [
                    'requests' => RequestResource::collection($requests['data']),
                    'meta' => $requests['meta']
                ]
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function createRequest($data)
    {

        try {
            // 1. Create the request
            $createdRequest = $this->requestService->createRequest($data);

            // 2. Prepare data for contract generation
            $contractData = [
                '{[client_name]}'       => $createdRequest['request']->user->username,
                '{[client_email]}'      => $createdRequest['request']->user->email,
                '{[client_phone]}'      => $createdRequest['request']->user->prefix . $createdRequest['request']->user->phone,

                '{[freelancer_name]}'   => $createdRequest['request']->service->user->username,
                '{[freelancer_email]}'  => $createdRequest['request']->service->user->email,
                '{[freelancer_phone]}'  => $createdRequest['request']->service->user->prefix . $createdRequest['request']->service->user->phone,

                '{[contract]}'   => $createdRequest['request']->order_number,
                '{[invoice]}'    => $createdRequest['request']->invoice_number ?? 'INV-' . $createdRequest['request']->id,
                '{[date]}'              => now()->format('Y-m-d'),

                '{[service_title]}'     => $createdRequest['request']->service->translations()->where('language', 'en')->first()->title,
                '{[delivery_date]}'     => $createdRequest['delivery_date'],

                '{[service_price]}'     => '$' . $createdRequest['finance']->amount,
                '{[commission]}'        => '$' . $createdRequest['finance']->commission,
                '{[tax]}'               => '$' . $createdRequest['finance']->fees,
                '{[total_amount]}'      => '$' . $createdRequest['finance']->total,

                '{[revisions]}'         => $createdRequest['revision'],

            ];


            // 3. Generate PDF contract
            $fileName = substr($createdRequest['request']->order_number, 1);

            $pdfUrl = $this->contractGenerator->generate($contractData, $fileName);

            // 4. Optionally save contract path to DB
            $createdRequest['request']->update([
                'contract_path' => $pdfUrl,
            ]);


            // 5. Send notification to freelancer
            $freelancer = $createdRequest['request']->service->user;

            $titles = [
                'en' => __('messages.new_request_title', [], 'en'),
                'ar' => __('messages.new_request_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_request_message', [
                    'order_number' => $createdRequest['request']->order_number
                ], 'en'),
                'ar' => __('messages.new_request_message', [
                    'order_number' => $createdRequest['request']->order_number
                ], 'ar'),
            ];

            $this->noticeService->send(
                $freelancer->id,
                $titles,
                $messages,
                'request',
                $createdRequest['request']->id,
                true
            );

            // mail to client
            Mail::to($createdRequest['request']->user->email)->queue(
                new NewRequestClientMail(
                    $createdRequest['request'],
                    $createdRequest['finance'],
                    $pdfUrl
                    // 'files/freelancer/f0c9395f3b1a5b6553d60be1d5fc792c.pdf'
                )
            );

            // mail to freelancer
            Mail::to($freelancer->email)->queue(
                new NewRequestFreelancerMail(
                    $createdRequest['request'],
                    $createdRequest['finance'],
                    $pdfUrl
                    // 'files/freelancer/f0c9395f3b1a5b6553d60be1d5fc792c.pdf'

                )
            );

            return $this->successResponse(__('success'), new RequestResource($createdRequest['request']));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function requestDetails($id)
    {
        try {
            $request = $this->requestService->getRequestDetails($id);
            // dd( $request );
            return $this->successResponse(__('success'), new RequestDetailsResource($request));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function addComment(AddRequestCommentRequest $request)
    {
        try {
            $data = array_merge($request->validated(), ['user_id' => Auth::id()]);

            $comment = $this->requestService->addComment($data);
            $result = $comment->load('user');

            return $this->successResponse(__('success'), new RequestCommentResource($result));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function confirmRequest($id)
    {
        $this->requestService->confirmRequest($id);
        return $this->successResponse(__('success'));
    }
}
