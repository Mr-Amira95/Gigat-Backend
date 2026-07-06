<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\WishlistService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\Api\WishlistToggleRequest;
use App\Services\MetaConversionsApiService;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function toggle(WishlistToggleRequest $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return $this->errorResponse(__('unauthorized'));
        }

        $added = $this->wishlistService->toggleWishlist(
            $userId,
            $request->service_id
        );

        if ($added && !Auth::user()->freelancer) {
            app(MetaConversionsApiService::class)->dispatchEvent(Auth::user(), $request, 'Client Add to Wishlist');
        }

        return $this->successResponse(__('messages.wishlist.toggle_success'));
    }

    public function getWishlistByUserId(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return $this->errorResponse(__('unauthorized'));
        }

        $services = $this->wishlistService->getWishlistByUserId($userId);

        return $this->successResponse(
            __('success'),
            ServiceResource::collection($services)
        );
    }
}
