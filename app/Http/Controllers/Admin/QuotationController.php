<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuotationService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    public function index(Request $request){
        $params = $request->all();
        $quotations = $this->quotationService->index($params);
        return view('pages.quotations.index',compact('quotations'));
    }
    public function show($id){
        $quotation = $this->quotationService->getQuotationDetails($id);
        return view('pages.quotations.show',compact('quotation'));
    }

    public function destroy($id)
    {
        $this->quotationService->delete($id);
        return redirect()->route('quotations.index')
            ->with('success', __('quotation_deleted_successfully'));
    }
}
