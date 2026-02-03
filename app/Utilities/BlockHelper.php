<?php

namespace App\Utilities;

use App\Models\Block;

class BlockHelper
{
    public static function isBlocked($userA, $userB): bool
    {
        if (!$userA || !$userB) return false;

        return Block::where(function ($q) use ($userA, $userB) {
                    $q->where('blocker_id', $userA)
                      ->where('blocked_id', $userB);
                })
                ->orWhere(function ($q) use ($userA, $userB) {
                    $q->where('blocker_id', $userB)
                      ->where('blocked_id', $userA);
                })
                ->exists();
    }
}
