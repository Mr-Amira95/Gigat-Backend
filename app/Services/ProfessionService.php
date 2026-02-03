<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ProfessionRepositoryInterface;

class ProfessionService
{
    protected $professionRepository;

    public function __construct(ProfessionRepositoryInterface $professionRepository)
    {
        $this->professionRepository = $professionRepository;
    }

    public function index()
    {
        return $this->professionRepository->index();
    }
    public function getAllActive()
    {
        return $this->professionRepository->getAllActive();
    }
    public function find($id)
    {
        return $this->professionRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->professionRepository->store($data);
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
            $result = $this->professionRepository->update($id, $data);
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
            $result = $this->professionRepository->delete($id);
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
            $profession = $this->professionRepository->updateActivation($id);
            return $profession;
        } catch (Exception $e) {
            throw $e;
        }
    }
} 