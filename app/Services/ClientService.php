<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ClientRepositoryInterface;

class ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index($params)
    {
        return $this->clientRepository->index($params);
    }
   
    public function store($data)
    {
        return $this->clientRepository->store($data);
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $result = $this->clientRepository->delete($id);

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
            $client = $this->clientRepository->updateActivation($id);
            return $client;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function find($id)
    {
        return $this->clientRepository->find($id);
    }

    public function getUserProfile($id)
    {
        return $this->clientRepository->getUserProfile($id);
    }

    public function updateProfile($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $client = $this->clientRepository->updateProfile($id, $data);
            
            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getArchived()
    {
        return $this->clientRepository->getArchived();
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->clientRepository->restore($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
