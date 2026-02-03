<?php

namespace App\Services;

use App\Repositories\Interfaces\WishlistRepositoryInterface;

class WishlistService
{
    protected $wishlistRepository;

    public function __construct(WishlistRepositoryInterface $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    public function toggleWishlist($userId, $serviceId)
    {
        return $this->wishlistRepository->toggleWishlist($userId, $serviceId);
    }

    public function getWishlistByUserId($userId)
    {
        return $this->wishlistRepository->getWishlistByUserId($userId);
    }
}
