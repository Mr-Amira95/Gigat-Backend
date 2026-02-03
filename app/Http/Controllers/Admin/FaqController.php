<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\FaqService;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;

class FaqController extends Controller
{
    protected $faqService;
    protected $categoryService;

    public function __construct(FaqService $faqService, CategoryService $categoryService)
    {
        $this->faqService = $faqService;
        $this->categoryService = $categoryService;
    }
    public function index(Request $request)
    {
        $faqs = $this->faqService->index($request->category_id);
        $categories = $this->categoryService->index();
        return view('pages.faqs.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryService->index();
        return view('pages.faqs.create', compact('categories'));
    }

    public function store(FaqRequest $request)
    {
        try {
            $this->faqService->store($request->validated());
            return redirect()->route('faqs.index')
                ->with('success', __('faq_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $faq = $this->faqService->find($id);
        $categories = $this->categoryService->index();
        return view('pages.faqs.edit', compact('faq', 'categories'));
    }

    public function update(FaqRequest $request, $id)
    {
        try {
            $this->faqService->update($id, $request->validated());
            return redirect()->route('faqs.index')
                ->with('success', __('faq_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function show($id)
    {
        $faq = $this->faqService->find($id);
        return view('pages.faqs.show', compact('faq'));
    }
    public function destroy($id)
    {
        try {
            $this->faqService->delete($id);
            return redirect()->back()
                ->with('success', __('faq_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function updateActivation(Request $request)
    {
        try {
            $faq = $this->faqService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
}
