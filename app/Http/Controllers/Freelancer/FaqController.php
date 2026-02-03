<?php

namespace App\Http\Controllers\Freelancer;

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
        return view('pages-freelancer.faqs.index', compact('faqs', 'categories'));
    }


}
