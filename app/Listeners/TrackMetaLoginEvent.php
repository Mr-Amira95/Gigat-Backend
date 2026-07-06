<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\MetaConversionsApiService;
use Illuminate\Auth\Events\Login;

class TrackMetaLoginEvent
{
    public function __construct(private MetaConversionsApiService $metaService) {}

    public function handle(Login $event): void
    {
        if ($event->guard !== 'freelancer') {
            return;
        }

        /** @var User $user */
        $user = $event->user;

        $this->metaService->dispatchEvent($user, request(), 'Freelancer Login');
    }
}
