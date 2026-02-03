<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Services\CategoryService;
use App\Services\FaqService;
use App\Services\PendingQuestionService;
use Illuminate\Http\Request;
use Exception;


class PendingQuestionController extends Controller
{
    protected $pendingQuestionService, $faqService;

    public function __construct(PendingQuestionService $pendingQuestionService, FaqService $faqService, CategoryService $categoryService)
    {
        $this->pendingQuestionService = $pendingQuestionService;
        $this->faqService = $faqService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $questions = $this->pendingQuestionService->index();
        return view('pages.pending-questions.index', compact('questions'));
    }

    public function show($id)
    {
        $question = $this->pendingQuestionService->find($id);
        $categories = $this->categoryService->index();
        return view('pages.pending-questions.show', compact('question', 'categories'));
    }

    public function destroy($id)
    {
        try {
            $this->pendingQuestionService->delete($id);
            return redirect()->back()
                ->with('success', __('pending_question_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function convertToFaq(FaqRequest $request, $id)
    {
        try {
            $this->faqService->store($request->validated());

            $this->pendingQuestionService->delete($id);

            return redirect()->route('pending-questions.index')
                ->with('success', __('pending_question_converted_to_faq'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
