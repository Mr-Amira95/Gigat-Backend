<?php

namespace App\Traits;

use App\Models\Block;

trait BlockFilterTrait
{
    protected static function bootBlockFilterTrait()
    {
        static::addGlobalScope('notBlocked', function ($query) {

            $authId = auth('api')->id() ?? auth('freelancer')->id();

            if ($authId) {
                // dd(auth('freelancer')->check());
                $blockedIds = Block::where('blocker_id', $authId)
                    ->pluck('blocked_id')
                    ->toArray();

                $blockedByIds = Block::where('blocked_id', $authId)
                    ->pluck('blocker_id')
                    ->toArray();

                $allBlocked = array_merge($blockedIds, $blockedByIds);

                if (!empty($allBlocked)) {
                    $query->whereNotIn('user_id', $allBlocked);
                }
            }
        });
    }
}
