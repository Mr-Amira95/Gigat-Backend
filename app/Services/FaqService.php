<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\FaqRepositoryInterface;

class FaqService
{
    protected $faqRepository;

    public function __construct(FaqRepositoryInterface $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    public function index($categoryId = null)
    {
        return $this->faqRepository->index($categoryId);
    }

    public function find($id)
    {
        return $this->faqRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->faqRepository->store($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->faqRepository->update($id, $data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->faqRepository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateActivation($id)
    {
        try {
            return $this->faqRepository->updateActivation($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getByCategory($categoryId)
    {
        return $this->faqRepository->getByCategory($categoryId);
    }
}
