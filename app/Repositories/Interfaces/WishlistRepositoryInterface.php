<?php

namespace App\Repositories\Interfaces;

interface WishlistRepositoryInterface
{
    public function toggleWishlist($userId, $serviceId);
    public function getWishlistByUserId($userId);
}
