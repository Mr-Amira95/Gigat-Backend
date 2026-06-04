<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\FinanceService;
use App\Services\FreelancerService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;


class FinanceController extends Controller
{
    protected FinanceService $financeService;
    protected FreelancerService $freelancerService;
    protected ClientService $clientService;

    public function __construct(FinanceService $financeService, FreelancerService $freelancerService, ClientService $clientService)
    {
        $this->financeService = $financeService;
        $this->freelancerService = $freelancerService;
        $this->clientService = $clientService;
    }
    // public function index()
    // {
    //     $finances = $this->financeService->getAll();

    //     $params = request()->all();
    //     $freelancers = $this->freelancerService->index($params);
    //     $clients = $this->clientService->index($params);

    //     return view('pages.finances.index', compact('finances', 'freelancers', 'clients'));
    // }
    public function index(Request $request)
    {
        $filters = $request->only([
            'client_id',
            'freelancer_id',
            'payment_status',
            'paid_date_from',
            'paid_date_to'
        ]);

        $finances = $this->financeService->getAllFiltered($filters);

        $params = request()->all();
        $clients = $this->clientService->index($params);
        $freelancers = $this->freelancerService->index($params);

        return view('pages.finances.index', compact('finances', 'clients', 'freelancers', 'filters'));
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'finance_ids' => 'required|array',
            'finance_ids.*' => 'required|numeric'
        ]);
        $this->financeService->bulkUpdate($data);
        return redirect()->route('finances.index')->with('success', __('finances_updated_successfully'));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'client_id',
            'freelancer_id',
            'payment_status',
            'paid_date_from',
            'paid_date_to'
        ]);

        // P2-06: export needs all rows — pass null to bypass pagination
        $finances = $this->financeService->getAllFiltered($filters, null);

        $fileName = 'finances_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        return response()->stream(function () use ($finances) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['#', 'Request ID', 'Client', 'Freelancer', 'Amount', 'Payment Status', 'Paid At']);

            foreach ($finances as $index => $finance) {
                fputcsv($handle, [
                    $index + 1,
                    $finance->request->order_number ?? '-',
                    $finance->request->user->username ?? '-',
                    $finance->request->service->user->username ?? '-',
                    number_format($finance->amount, 2),
                    \App\Enums\PaymentStatusEnum::tryFrom($finance->payment_status)?->name ?? '-',
                    $finance->paid_at ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
