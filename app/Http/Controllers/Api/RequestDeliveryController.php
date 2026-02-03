<?php

namespace App\Http\Controllers\Api;

use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestDeliveryRequest;
use App\Http\Requests\Api\UpdateRequestDeliveryRequest;
use App\Http\Resources\RequestDeliveryResource;
use App\Models\Request as RequestModel;
use App\Services\RequestDeliveryService;
use App\Services\RequestService;
use App\Traits\BaseResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestDeliveryController extends Controller
{
    use BaseResponse;

    protected $requestDeliveryService;
    protected $requestService;

    public function __construct(RequestDeliveryService $requestDeliveryService, RequestService $requestService)
    {
        $this->requestDeliveryService = $requestDeliveryService;
        $this->requestService = $requestService;
    }

    public function store(RequestDeliveryRequest $request, $id)
    {
        try {

            // 1. Fetch request
            $req = RequestModel::findOrFail($id);

            // 2. Request must be in progress to allow delivery
            if ($req->status !== RequestStatusEnum::IN_PROGRESS->value) {
                return $this->errorResponse(__('request_cannot_be_delivered_status'));
            }

            // 3. Save delivery action in LOGS first
            $logData = [
                'user_id'     => Auth::id(),
                'request_id'  => $id,
                'status'      => RequestStatusEnum::COMPLETED->value,
                'action'      => $request->message,
                'attachments' => $request->file('attachments', []),
            ];

            $this->requestService->addComment($logData);

            // 4. Prepare data for delivery repository
            $deliveryData = [
                'request_id'  => $id,
                'message'     => $request->message,
                'attachments' => $request->file('attachments', []),
            ];

            // 5. Create delivery (repo handles upload + translation)
            $delivery = $this->requestDeliveryService->create($deliveryData);


            // 6. Return response
            return $this->successResponse(
                __('delivery_submitted_successfully'),
                new RequestDeliveryResource($delivery)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }



    public function update(UpdateRequestDeliveryRequest $request, $id, $deliveryId)
    {
        try {

            // 1. Fetch the request
            $req = RequestModel::findOrFail($id);

            // 2. Fetch delivery by ID
            $delivery = $this->requestDeliveryService->findById($deliveryId);

            // Safety check delivery belongs to the request
            if (!$delivery || $delivery->request_id != $id) {
                return $this->errorResponse(__('no_delivery_found'));
            }

            // 3. Prepare update data (message + attachments only)
            $data = [
                'message'     => $request->message,
                'attachments' => $request->file('attachments', []),
            ];

            // 4. Update the delivery -> Repository handles translation + uploads
            $updatedDelivery = $this->requestDeliveryService->update($delivery, $data);

            // 5. Return updated delivery
            return $this->successResponse(
                __('delivery_updated_successfully'),
                new RequestDeliveryResource($updatedDelivery)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function deleteAttachment($id)
    {
        try {
            $deleted = $this->requestDeliveryService->deleteAttachmentById($id);

            if (!$deleted) {
                return $this->errorResponse(__('attachment_not_found'));
            }

            return $this->successResponse(__('attachment_deleted_successfully'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function getRequestDeliveries($requestId)
    {
        try {
            $deliveries = $this->requestDeliveryService->getDeliveriesByRequestId($requestId);

            return $this->successResponse(
                __('success'),
                RequestDeliveryResource::collection($deliveries)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
