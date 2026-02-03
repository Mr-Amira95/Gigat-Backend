<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReleaseResource;
use App\Services\ReleaseService;
use App\Traits\BaseResponse;
use Exception;

class ReleaseController extends Controller
{
    use BaseResponse;

    protected $releaseService;

    public function __construct(ReleaseService $releaseService)
    {
        $this->releaseService = $releaseService;
    }


    public function index()
    {
        try {
            $releases = $this->releaseService->getAll();
            return $this->successResponse(
                __('success'),
                ReleaseResource::collection($releases)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function show($id)
    {
        try {
            $release = $this->releaseService->getById($id);

            if (!$release) {
                return $this->errorResponse(__('release_unavailable'), 404);
            }

            return $this->successResponse(
                __('success'),
                new ReleaseResource($release)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
