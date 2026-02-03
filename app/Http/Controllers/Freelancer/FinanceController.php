<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use App\Services\RequestService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index(Request $request)
    {
        $statusFilter = $request->query('payment_status');

        // Base query for all finances (for the logged-in freelancer)
        $baseQuery = $this->financeService->getForFreelancer();

        // Clone query for table results (filtered by status if needed)
        $financesQuery = clone $baseQuery;

        if ($statusFilter === 'paid') {
            $financesQuery->where('payment_status', \App\Enums\PaymentStatusEnum::PAID->value);
        } elseif ($statusFilter === 'unpaid') {
            $financesQuery->where('payment_status', '!=', \App\Enums\PaymentStatusEnum::PAID->value);
        }

        $finances = $financesQuery->get();

        // Helper closure for net income (amount - commission)
        $netAmount = fn($f) => ($f->amount ?? 0) - ($f->commission ?? 0);

        // Totals (always on unfiltered data)
        $allFinances = $baseQuery->get();
        $totalUsd       = $allFinances->sum($netAmount);
        $pendingUsd     = $allFinances->where('payment_status', \App\Enums\PaymentStatusEnum::UNPAID->value)->sum($netAmount);
        $transferredUsd = $allFinances->where('payment_status', \App\Enums\PaymentStatusEnum::PAID->value)->sum($netAmount);

        return view('pages-freelancer.finances.index', compact(
            'finances',
            'statusFilter',
            'totalUsd',
            'pendingUsd',
            'transferredUsd'
        ));
    }

    // public function bulkUpdate(Request $request){
    //    $data = $request->validate([
    //     'finance_ids'=>'required|array',
    //     'finance_ids.*'=>'required|numeric'
    //    ]);
    //    $this->financeService->bulkUpdate($data);
    //    return redirect()->route('finances.index')->with('success', __('finances_updated_successfully'));

    // }
}
