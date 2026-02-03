<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Services\PortfolioService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PortfolioResource;
use App\Http\Requests\Api\PortfolioRequest;
use App\Http\Requests\Api\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioDetalisResource;
use App\Models\PortfolioMedia;

class PortfolioController extends Controller
{
    protected $portfolioService;
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }
    public function getPortfolioByUserId(Request $request)
    {
        $userId = $request->input('user_id', Auth::guard('api')->id());


        $portfolio = $this->portfolioService->getPortfolioByUserId($userId);
        return $this->successResponse(__('success'), [
            'portfolios' => PortfolioResource::collection($portfolio['data']),
            'meta' => $portfolio['meta']
        ]);
    }
    public function getFeaturedPortfolios(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $portfolios = $this->portfolioService->getFeaturedPortfolios($perPage);
        return $this->successResponse(__('success'), [
            'portfolios' => PortfolioResource::collection($portfolios['data']),
            'meta' => $portfolios['meta']
        ]);
    }
    public function portfolioDetails($id)
    {
        $portfolio = $this->portfolioService->getPortfolioDetails($id);
        return $this->successResponse(__('success'), new PortfolioDetalisResource($portfolio));
    }
    public function create(PortfolioRequest $request)
    {
        try {
            $data = $request->validated();
            $data = array_merge($data, ['user_id' => Auth::id()]);
            $portfolio = $this->portfolioService->create($data);
            return $this->successResponse(__('success'), new PortfolioDetalisResource($portfolio));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function update(UpdatePortfolioRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data = array_merge($data, ['user_id' => Auth::id()]);
            $portfolio = $this->portfolioService->update($data, $id);
            return $this->successResponse(__('success'), new PortfolioDetalisResource($portfolio));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function delete($id)
    {
        try {
            $this->portfolioService->delete($id);
            return $this->successResponse(__('success'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function deleteMedia($id)
    {

        try {
            $userId = Auth::id();

            // Find the media that belongs to one of the freelancer's Portfolio
            $media = PortfolioMedia::whereHas('portfolio', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->find($id);

            if (!$media) {
                return response()->json(['message' => 'Unauthorized access'], 403);
            }

            if ($media->path && \Storage::exists($media->path)) {
                $this->portfolioService->deleteMedia($id);
                return $this->successResponse(__('success'));
            }

            $media->delete();

            return $this->successResponse(__('success'));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
