<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\PendingQuestionRepositoryInterface;

class PendingQuestionService
{
    protected $pendingQuestionRepository;

    public function __construct(PendingQuestionRepositoryInterface $pendingQuestionRepository)
    {
        $this->pendingQuestionRepository = $pendingQuestionRepository;
    }
    public function index()
    {
        return $this->pendingQuestionRepository->index();
    }

    public function find($id)
    {
        return $this->pendingQuestionRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $question = $this->pendingQuestionRepository->store($data);
            DB::commit();
            return $question;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $deleted = $this->pendingQuestionRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
