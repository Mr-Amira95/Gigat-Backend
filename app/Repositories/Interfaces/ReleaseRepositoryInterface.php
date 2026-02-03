<?php

namespace App\Repositories\Interfaces;

use App\Models\Release;
use Illuminate\Support\Collection;

interface ReleaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function allForAdmin();
    public function findForAdmin($id);
    public function latest();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation($id);
}
