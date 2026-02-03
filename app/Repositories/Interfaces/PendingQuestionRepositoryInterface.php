<?php

namespace App\Repositories\Interfaces;

interface PendingQuestionRepositoryInterface
{
    public function index();
    public function find($id);
    public function store($data);
    public function delete($id);
}
