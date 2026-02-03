<?php

namespace App\Http\Controllers\Api;

use App\Services\FaqService;
use Illuminate\Http\Request;
use App\Http\Resources\FaqResource;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    protected $faqService;
    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
    }
    public function index(Request $request)
    {
        $faqs = $this->faqService->index($request->category_id);
        return $this->successResponse(__('success'),FaqResource::collection($faqs));
    }
}
