<?php
namespace App\Observers;

use App\Models\User;
use App\Utilities\AvatarGenerator;

class UserObserver
{

    public function creating(User $user)
    {
        if (!$user->avatar) {
            $user->avatar = AvatarGenerator::getAvatarForName($user->username);
        }
    }
}
