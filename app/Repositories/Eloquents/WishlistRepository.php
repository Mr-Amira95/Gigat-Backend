<?php

namespace App\Repositories\Eloquents;

use App\Models\Wishlist;
use App\Models\Service;
use App\Repositories\Interfaces\WishlistRepositoryInterface;

class WishlistRepository implements WishlistRepositoryInterface
{
    public function toggleWishlist($userId, $serviceId)
    {
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();

        if ($wishlist) {
            return $wishlist->delete();
        }

        return (bool) Wishlist::create([
            'user_id' => $userId,
            'service_id' => $serviceId
        ]);
    }

    public function getWishlistByUserId($userId)
    {
        return Service::whereHas('serviceWishlist', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('is_active', true)
            ->with(['media', 'user.profession'])
            ->get();
    }
}
