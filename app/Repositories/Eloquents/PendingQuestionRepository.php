<?php

namespace App\Repositories\Eloquents;

use App\Models\PendingQuestion;
use App\Repositories\Interfaces\PendingQuestionRepositoryInterface;

class PendingQuestionRepository implements PendingQuestionRepositoryInterface
{
    protected $model;

    public function __construct(PendingQuestion $pendingQuestion)
    {
        $this->model = $pendingQuestion;
    }

    public function index()
    {
        return $this->model->orderByDesc('id')->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
