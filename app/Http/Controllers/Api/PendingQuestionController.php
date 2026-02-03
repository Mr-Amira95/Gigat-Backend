<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PendingQuestionRequest;
use App\Services\PendingQuestionService;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;

class PendingQuestionController extends Controller
{
    use BaseResponse;

    protected $pendingService;

    public function __construct(PendingQuestionService $pendingQuestionService)
    {
        $this->pendingQuestionService = $pendingQuestionService;
    }

    public function store(PendingQuestionRequest $request)
    {

        $this->pendingQuestionService->store([
            'question' => $request->question,
        ]);

        return $this->successResponse(__('question_submitted_successfully'));
    }
}
