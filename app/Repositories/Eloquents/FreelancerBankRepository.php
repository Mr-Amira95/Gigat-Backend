<?php

namespace App\Repositories\Eloquents;

use App\Models\FreelancerBank;
use App\Repositories\Interfaces\FreelancerBankRepositoryInterface;
use Illuminate\Support\Facades\Crypt;

class FreelancerBankRepository implements FreelancerBankRepositoryInterface
{
    protected $model;

    public function __construct(FreelancerBank $model)
    {
        $this->model = $model;
    }

    public function index($freelancerId)
    {
        return $this->model->where('freelancer_id', $freelancerId)->first();
    }

    public function updateOrCreate($freelancerId, $data)
    {
        // Encrypt account number if needed
        // if (isset($data['account_number'])) {
        //     $data['account_number'] = Crypt::encryptString($data['account_number']);
        // }

        return $this->model->updateOrCreate(
            ['freelancer_id' => $freelancerId],
            $data
        );
    }
}
