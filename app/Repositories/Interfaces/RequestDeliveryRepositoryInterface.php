<?php

namespace App\Repositories\Interfaces;

use App\Models\RequestDelivery;

interface RequestDeliveryRepositoryInterface
{
    public function findById($id);

    public function create($data);

    public function update(RequestDelivery $delivery, $data);

    public function deleteAttachmentById($attachmentId);

    public function getDeliveriesByRequestId($requestId);
}
