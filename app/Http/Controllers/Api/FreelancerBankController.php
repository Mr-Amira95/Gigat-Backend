<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BankDetailsRequest;
use App\Http\Resources\FreelancerBankResource;
use App\Services\FreelancerBankService;
use Illuminate\Http\Request;

class FreelancerBankController extends Controller
{
    protected $bankService;

    public function __construct(FreelancerBankService $bankService)
    {
        $this->bankService = $bankService;
    }
    public function index()
    {
        try {
            $freelancerId = auth('api')->id();
            $bank = $this->bankService->index($freelancerId);

            if (!$bank) {
                return $this->successResponse(__('success'), null);
            }
            return $this->successResponse(__('success'), new FreelancerBankResource($bank));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function updateOrCreate(BankDetailsRequest $request)
    {
        try {
            $freelancerId = auth('api')->id();

            $data = $request->validated();

            $bank = $this->bankService->updateOrCreate($freelancerId, $data);

            return $this->successResponse(__('success'), new FreelancerBankResource($bank));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
