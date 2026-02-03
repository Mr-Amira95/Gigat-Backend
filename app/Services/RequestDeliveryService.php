<?php

namespace App\Services;

use App\Repositories\Interfaces\RequestDeliveryRepositoryInterface;

class RequestDeliveryService
{
    protected $requestDeliveryRepository;

    public function __construct(RequestDeliveryRepositoryInterface $requestDeliveryRepository)
    {
        $this->requestDeliveryRepository = $requestDeliveryRepository;
    }

    public function findById($id)
    {
        return $this->requestDeliveryRepository->findById($id);
    }

    public function create($data)
    {
        return $this->requestDeliveryRepository->create($data);
    }

    public function update($delivery, $data)
    {
        return $this->requestDeliveryRepository->update($delivery, $data);
    }

    public function deleteAttachmentById($attachmentId)
    {
        return $this->requestDeliveryRepository->deleteAttachmentById($attachmentId);
    }

    public function getDeliveriesByRequestId($requestId)
    {
        return $this->requestDeliveryRepository->getDeliveriesByRequestId($requestId);
    }
}
