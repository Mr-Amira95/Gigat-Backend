<?php

namespace App\Services;

use App\Repositories\AgoraRepository;

class AgoraService
{
    protected $repository;

    public function __construct(AgoraRepository $repository)
    {
        $this->repository = $repository;
    }
}
