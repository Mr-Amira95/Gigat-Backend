<?php

namespace App\Http\Controllers\Api;

use App\Enums\RequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\HireFreelancerRequest;
use App\Http\Resources\RequestDetailsResource;
use App\Http\Resources\RequestResource;
use App\Services\ServiceService;
use App\Services\RequestService;
use App\Utilities\CurrencyConverter;
use App\Mail\NewRequestClientMail;
use App\Mail\NewRequestFreelancerMail;
use App\Models\Plan;
use App\Models\SubCategory;
use App\Models\User;
use App\Services\ContractGeneratorService;
use App\Services\NoticeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HireFreelancerController extends Controller
{

    private $serviceService;
    private $requestService;
    private $contractGenerator;
    private $noticeService;

    public function __construct(ServiceService $serviceService, RequestService $requestService, ContractGeneratorService $contractGenerator, NoticeService $noticeService)
    {
        $this->serviceService = $serviceService;
        $this->requestService = $requestService;
        $this->contractGenerator = $contractGenerator;
        $this->noticeService = $noticeService;
    }

    public function store(HireFreelancerRequest $request)
    {
        DB::beginTransaction();

        try {

            $client = auth()->user();
            $freelancer = User::find($request->freelancer_id);
            $validated = $request->validated();

            $priceUsd = CurrencyConverter::convert(
                $validated['price'],
                $validated['currency'],
                'USD'
            );

            $planId = Plan::first()->id;
            $subCategoryId = SubCategory::first()->id;

            $serviceData = [
                'user_id'         => $freelancer->id,
                'sub_category_id' => $subCategoryId,
                'title'           => $validated['service_title'],
                'description'     => $validated['service_description'],
                'plans' => [
                    [
                        'plan_id' => $planId,
                        'features' => [
                            ['type' => 'price',         'value' => $priceUsd],
                            ['type' => 'delivery_days', 'value' => $validated['delivery_days']],
                            ['type' => 'revisions',     'value' => $validated['revisions']],
                            ['type' => 'source_files',  'value' => $validated['source_files']],
                        ]
                    ]
                ]
            ];

            $service = $this->serviceService->create($serviceData);

            $requestPayload = [
                'user_id'              => $client->id,
                'freelancer_id'        => $freelancer->id,
                'service_id'           => $service->id,
                'plan_id'              => $planId,
                'client_payment_status' => 'pending',
            ];

            // Reuse main flow
            $requestController = app(RequestController::class);
            $response = $requestController->createRequest($requestPayload);

            // Extract JSON response safely
            $responseData = $response->getData(true);

            if (!isset($responseData['data']['id'])) {
                throw new \Exception('Request creation failed');
            }

            $requestId = $responseData['data']['id'];

            // Get fresh request model
            $requestModel = $this->requestService->getRequestDetails($requestId);

            // Add initial attachments as log
            if ($request->hasFile('attachments')) {
                $this->requestService->addComment([
                    'request_id'  => $requestId,
                    'status'      => $requestModel->status,
                    'action'      => 'Initial service attachments',
                    'attachments' => $request->file('attachments'),
                ]);
            }

            // Soft delete temporary service
            $service->delete();

            // Reload full details after attachments
            $requestModel = $this->requestService->getRequestDetails($requestId);

            DB::commit();

            return $this->successResponse(
                __('freelancer_hired_successfully'),
                new RequestDetailsResource($requestModel)
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->exceptionResponse($e);
        }
    }

    // public function payRequest($id)
    // {
    //     try {
    //         $response = $this->requestService->payRequest($id);

    //         return $this->successResponse(__('success'), $response);
    //     } catch (\Exception $e) {
    //         return $this->exceptionResponse($e);
    //     }
    // }
}
