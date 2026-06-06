<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Services\AnalyticsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VisitorRequest;
use App\Http\Requests\Api\AnalyticsEventRequest;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * POST /api/analytics/visitor
     */
    public function visitor(VisitorRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['ip_address'] = $request->ip();

            $result = $this->analyticsService->upsertVisitor($data);

            $status = $result['created'] ? 201 : 200;

            return $this->successResponse(__('success'), [
                'visitor_uuid' => $result['visitor']->visitor_uuid,
            ], $status);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * POST /api/analytics/event
     */
    public function event(AnalyticsEventRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($userId = auth('api')->id()) {
                $data['user_id'] = $userId;
            }

            $this->analyticsService->recordEvent($data);

            return $this->successResponse(__('success'), null, 201);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
