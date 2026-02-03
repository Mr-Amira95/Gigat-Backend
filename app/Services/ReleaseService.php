<?php

namespace App\Services;

use App\Repositories\Interfaces\ReleaseRepositoryInterface;

class ReleaseService
{
    protected $releaseRepository;

    public function __construct(ReleaseRepositoryInterface $releaseRepository)
    {
        $this->releaseRepository = $releaseRepository;
    }

    public function getAll()
    {
        return $this->releaseRepository->all();
    }

    public function getById($id)
    {
        return $this->releaseRepository->find($id);
    }

    public function getLatest()
    {
        return $this->releaseRepository->latest();
    }

    public function getAllForAdmin()
    {
        return $this->releaseRepository->allForAdmin();
    }
    public function getByIdForAdmin($id)
    {
        return $this->releaseRepository->findForAdmin($id);
    }
    public function create($data)
    {
        return $this->releaseRepository->create($data);
    }

    public function update($id, $data)
    {
        return $this->releaseRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->releaseRepository->delete($id);
    }
    public function updateActivation($id)
    {
        return $this->releaseRepository->updateActivation($id);
    }
}
