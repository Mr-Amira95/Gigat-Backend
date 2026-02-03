<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompanyRequest;
use App\Http\Requests\Api\RegisterCompanyRequest;
use App\Http\Requests\Api\UpdateCompanyRequest;
use App\Http\Resources\CompanyDetailsResource;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function registerCompany(RegisterCompanyRequest $request)
    {
        try {
            $data = $request->validated();

            $this->companyService->registerCompany($data);

            return $this->successResponse(__('success'), 201);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function store(CompanyRequest $request)
    {
        try {
            $data = $request->validated();

            $data['freelancer_id'] = auth('api')->id();

            $companyId = $this->companyService->getFreelancerCompanyId($data['freelancer_id']);

            if ($companyId) {
                return $this->errorResponse(__('unauthorized'), 403);
            }

            $company = $this->companyService->store($data);

            return $this->successResponse(__('success'), new CompanyDetailsResource($company));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function update(UpdateCompanyRequest $request)
    {
        try {
            $userId = auth('api')->id();

            $companyId = $this->companyService->getFreelancerCompanyId($userId);

            if (!$companyId) {
                return $this->errorResponse(__('unauthorized'), 403);
            }

            $data = $request->validated();

            $company = $this->companyService->update($companyId, $data);

            return $this->successResponse(__('success'), new CompanyDetailsResource($company));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function getFreelancerCompany()
    {
        try {
            $userId = auth('api')->id();
            $companyId = $this->companyService->getFreelancerCompanyId($userId);

            if (!$companyId) {
                return $this->errorResponse(__('unauthorized'), 403);
            }

            $company = $this->companyService->find($companyId);

            return $this->successResponse(__('success'), new CompanyDetailsResource($company));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function getCompanyById($companyId)
    {
        try {
            $company = $this->companyService->find($companyId);

            return $this->successResponse(__('success'), new CompanyDetailsResource($company));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
