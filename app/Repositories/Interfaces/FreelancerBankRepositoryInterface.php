<?php

namespace App\Repositories\Interfaces;

use App\Models\FreelancerBank;

interface FreelancerBankRepositoryInterface
{
    public function index($freelancerId);

    public function updateOrCreate($freelancerId, $data);
}
