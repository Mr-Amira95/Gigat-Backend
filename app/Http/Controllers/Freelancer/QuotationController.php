<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationComment;
use App\Services\QuotationService;
use App\Utilities\GoogleTranslator;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    public function index()
    {
        $quotations = $this->quotationService->getQuotationsForFreelancerWithComment();
        // dd($quotations);
        return view('pages-freelancer.quotations.index', compact('quotations'));
    }
    public function show($id)
    {
        $quotation = $this->quotationService->getQuotationDetails($id);
        return view('pages-freelancer.quotations.show', compact('quotation'));
    }

    public function createComment($quotationId)
    {
        $quotation = Quotation::findOrFail($quotationId);
        return view('pages-freelancer.quotations.create', compact('quotation'));
    }

    public function storeComment(Request $request, $quotationId)
    {

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = QuotationComment::create([
            'quotation_id' => $quotationId,
            'user_id'      => auth()->id(),
        ]);

        $googleTranslator = new GoogleTranslator();
        $translations = $googleTranslator->translateForStorage($request->comment);

        foreach (['en', 'ar'] as $lang) {
            $comment->translations()->create([
                'language' => $lang,
                'comment'  => $translations[$lang],
            ]);
        }

        return redirect()->route('freelancer.quotations.show', $quotationId)
            ->with('success', 'Comment added successfully.');
    }
}
