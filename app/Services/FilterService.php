<?php

namespace App\Services;

use App\Repositories\Eloquents\FilterRepository;


class FilterService
{
    protected $filterRepository;

    public function __construct(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }
    public function index($categoryId = null)
    {
        return $this->filterRepository->index($categoryId);
    }
    public function store(array $data)
    {       
        return $this->filterRepository->store($data);
    }

    public function update($id, array $data)
    {
        return $this->filterRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->filterRepository->delete($id);
    }

    public function find($id)
    {
        return $this->filterRepository->find($id);
    }
    public function getFiltersByCategoryId($categoryId)
    {
        return $this->filterRepository->getFiltersByCategoryId($categoryId);
    }
}
