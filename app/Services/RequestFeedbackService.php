<?php

namespace App\Services;

use App\Repositories\Interfaces\RequestFeedbackRepositoryInterface;

class RequestFeedbackService
{
    protected $requestFeedbackRepository;

    public function __construct(RequestFeedbackRepositoryInterface $requestFeedbackRepository)
    {
        $this->requestFeedbackRepository = $requestFeedbackRepository;
    }

    public function create($data)
    {
        return $this->requestFeedbackRepository->create($data);
    }
}
