<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\FinanceClientResource;
use App\Http\Resources\FinanceFreelancerResource;
use App\Services\FinanceService;
use App\Utilities\CurrencyConverter;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }
    public function getClientFinancialRecords()
    {
        try {
            $records = $this->financeService->getClientFinancialRecords();

            // get currency header (default USD)
            $currencyCode = request()->header('currency', 'USD');
            // sum all amounts (base = USD) then convert
            $totalUsd = $records->sum(function ($finance) {
                return ($finance->amount ?? 0) + ($finance->fees ?? 0);
            });

            $totalConverted = $totalUsd
                ? CurrencyConverter::convert($totalUsd, 'USD', $currencyCode)
                : 0;

            return $this->successResponse(__('success'), [
                'total_amount' =>  $totalConverted,
                'transactions'      => FinanceClientResource::collection($records),
            ]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function getFreelancerFinancialRecords()
    {
        try {
            $records = $this->financeService->getFreelancerFinancialRecords();

            // filter on the transactions list
            $status = request()->query('status');
            $filtered = $records;

            if ($status === 'paid') {
                $filtered = $records->where('payment_status', PaymentStatusEnum::PAID->value);
            } elseif ($status === 'unpaid') {
                $filtered = $records->where('payment_status', PaymentStatusEnum::UNPAID->value);
            }


            $currencyCode  = request()->header('currency', 'USD');

            $totalUsd = $records->sum(function ($finance) {
                return ($finance->amount ?? 0) - ($finance->commission ?? 0);
            });

            $pendingUsd = $records
                ->where('payment_status', PaymentStatusEnum::UNPAID->value)
                ->sum(function ($finance) {
                    return ($finance->amount ?? 0) - ($finance->commission ?? 0);
                });

            $transferredUsd = $records
                ->where('payment_status', PaymentStatusEnum::PAID->value)
                ->sum(function ($finance) {
                    return ($finance->amount ?? 0) - ($finance->commission ?? 0);
                });

            $totalConv       = $totalUsd       ? CurrencyConverter::convert($totalUsd, 'USD', $currencyCode) : 0;
            $pendingConv     = $pendingUsd     ? CurrencyConverter::convert($pendingUsd, 'USD', $currencyCode) : 0;
            $transferredConv = $transferredUsd ? CurrencyConverter::convert($transferredUsd, 'USD', $currencyCode) : 0;


            return $this->successResponse(__('success'), [
                'total_income'         => $totalConv,
                'pending_income'       => $pendingConv,
                'transferred_income' =>  $transferredConv,
                'transactions'              => FinanceFreelancerResource::collection($filtered),
            ]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
