<?php

namespace App\Services;

use App\Models\Freelancer;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\FreelancerRepositoryInterface;

class FreelancerService
{
    protected $freelancerRepository;

    public function __construct(FreelancerRepositoryInterface $freelancerRepository, private readonly ServiceService $serviceService)
    {
        $this->freelancerRepository = $freelancerRepository;
    }

    public function index($params)
    {
        return $this->freelancerRepository->index($params);
    }
    public function getByAuthUser()
    {
        return $this->freelancerRepository->getByAuthUser();
    }
    public function store($data)
    {
        return $this->freelancerRepository->store($data);
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $result = $this->freelancerRepository->delete($id);

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
            $freelancer = $this->freelancerRepository->updateActivation($id);
            return $freelancer;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function updateVerification($id)
    {
        try {
            return $this->freelancerRepository->updateVerification($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function find($id)
    {
        return $this->freelancerRepository->find($id);
    }

    public function completeProfile(array $data)
    {
        try {
            DB::beginTransaction();

            $freelancer = $this->freelancerRepository->completeProfile($data);

            DB::commit();
            return $freelancer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserProfile($id)
    {
        return $this->freelancerRepository->getUserProfile($id);
    }
    public function updateProfile($id, array $data)
    {
        try {
            DB::beginTransaction();

            $freelancer = $this->freelancerRepository->updateProfile($id, $data);

            DB::commit();
            return $freelancer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getArchived()
    {
        return $this->freelancerRepository->getArchived();
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->freelancerRepository->restore($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
