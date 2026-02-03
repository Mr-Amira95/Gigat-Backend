<?php

namespace App\Http\Controllers\Api;

use App\Models\PlanFeature;
use Illuminate\Http\Request;
use App\Services\PlanService;
use App\Services\ServiceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ServiceDetailsResource;
use App\Models\Currency;
use App\Models\General;
use App\Models\Plan;
use App\Models\Quotation;
use App\Models\QuotationComment;
use App\Utilities\CurrencyConverter;

class CheckoutController extends Controller
{
    protected $serviceService;
    protected $planService;
    public function __construct(ServiceService $serviceService, PlanService $planService)
    {

        $this->serviceService = $serviceService;
        $this->planService = $planService;
    }
    // public function proceedCheckout(Request $request)
    // {

    //     $data = $request->validate([
    //         'service_id' => 'required',
    //         'plan_id' => 'required',
    //     ]);
    //     $service = $this->serviceService->getServiceDetails($data['service_id']);
    //     $plan = $this->planService->find($data['plan_id']);
    //     $planFeatures = $service->features()->where('plan_id', $data['plan_id'])->get();
    //     $planPrice = $planFeatures->where('type', 'price')->first();
    //     $fees = General::where('key', 'fees')->first()->value / 100;
    //     $commission = General::where('key', 'commission')->first()->value / 100;

    //     $currencyCode = $request->header('currency', 'USD');
    //     $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
    //     $symbol = $currencyModel ? $currencyModel->symbol : '$';
    //     // dd($planPrice->value);
    //     $price = (float) str_replace(',', '', $planPrice->value);

    //     $planPriceConverted = $price ? CurrencyConverter::convert($price, 'USD', $currencyCode) : null;
    //     $feesConverted = ($price * $fees) ? CurrencyConverter::convert(($price * $fees), 'USD', $currencyCode) : null;
    //     $commissionConverted = ($price * $commission) ? CurrencyConverter::convert(($price * $commission), 'USD', $currencyCode) : null;

    //     // Remove thousands separators (commas) and cast to float to ensure accurate numeric calculations
    //     $planPriceConverted = (float) str_replace(',', '', $planPriceConverted);
    //     $feesConverted = (float) str_replace(',', '', $feesConverted);
    //     $commissionConverted = (float) str_replace(',', '', $commissionConverted);

    //     // $total = ($planPriceConverted) + ($feesConverted) + ($commissionConverted);
    //     $total = ($planPriceConverted) + ($feesConverted);

    //     return $this->successResponse('success', [
    //         'request_info' => [
    //             'title' => $service->translation->title,
    //             'description' => $service->translation->description,
    //             'plan_title' => $plan->translation->title,
    //             'sub_total' => $planPriceConverted ? number_format((float)$planPriceConverted, 2) . ' ' .  $symbol  : null,
    //             'fees' => $feesConverted ? number_format((float)$feesConverted, 2) . ' ' . $symbol  : null,
    //             'commission' => $commissionConverted ? number_format((float)$commissionConverted, 2) . ' ' . $symbol  : null,
    //             'total' => $total ? number_format((float)$total, 2) . ' ' . $symbol  : null,

    //         ],
    //     ]);
    // }
    public function proceedCheckout(Request $request)
    {
        $data = $request->validate([
            // Quotation flow
            'quotation_id' => 'nullable|integer|exists:quotations,id|required_without:service_id',
            'comment_id'   => 'nullable|integer|exists:quotation_comments,id|required_with:quotation_id',

            // Service flow
            'service_id'   => 'nullable|integer|exists:services,id|required_without:quotation_id',
            'plan_id'      => 'nullable|integer|exists:plans,id|required_with:service_id',
        ]);

        if (!empty($data['quotation_id']) && !empty($data['comment_id'])) {
            // 👇 Quotation flow
            $quotation = Quotation::findOrFail($data['quotation_id']);
            $comment   = QuotationComment::findOrFail($data['comment_id']);

            $plan  = Plan::first();
            $price = (float) $quotation->price;
            $title = $quotation->translation->title;
            $description = $quotation->translation->description;

            // fees, commission
            $feesRate = General::where('key', 'fees')->first()->value / 100;
            $commissionRate = General::where('key', 'commission')->first()->value / 100;

            $currencyCode = $request->header('currency', 'USD');
            $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
            $symbol = $currencyModel ? $currencyModel->symbol : '$';


            $planPriceConverted = CurrencyConverter::convert($price, 'USD', $currencyCode);
            $feesConverted = CurrencyConverter::convert(($price * $feesRate), 'USD', $currencyCode);
            $commissionConverted = CurrencyConverter::convert(($price * $commissionRate), 'USD', $currencyCode);

            // normalize
            $planPriceConverted = (float) str_replace(',', '', $planPriceConverted);
            $feesConverted = (float) str_replace(',', '', $feesConverted);
            $commissionConverted = (float) str_replace(',', '', $commissionConverted);

            $total = $planPriceConverted + $feesConverted;
        } else {
            // 👇 Handle normal service/plan flow (your current code)
            $service = $this->serviceService->getServiceDetails($data['service_id']);
            $plan = $this->planService->find($data['plan_id']);
            $planFeatures = $service->features()->where('plan_id', $data['plan_id'])->get();
            $planPrice = $planFeatures->where('type', 'price')->first();
            $price = (float) str_replace(',', '', $planPrice->value);

            $fees = General::where('key', 'fees')->first()->value / 100;
            $commission = General::where('key', 'commission')->first()->value / 100;

            $currencyCode = $request->header('currency', 'USD');
            $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
            $symbol = $currencyModel ? $currencyModel->symbol : '$';

            $planPriceConverted = CurrencyConverter::convert($price, 'USD', $currencyCode);
            $feesConverted = CurrencyConverter::convert($price * $fees, 'USD', $currencyCode);
            $commissionConverted = CurrencyConverter::convert($price * $commission, 'USD', $currencyCode);

            // ✅ Normalize values
            $planPriceConverted = (float) str_replace(',', '', $planPriceConverted);
            $feesConverted = (float) str_replace(',', '', $feesConverted);
            $commissionConverted = (float) str_replace(',', '', $commissionConverted);

            $total = $planPriceConverted + $feesConverted;
            $title = $service->translation->title;
            $description = $service->translation->description;
        }

        return $this->successResponse('success', [
            'request_info' => [
                'title'       => $title ?? null,
                'description' => $description ?? null,
                'plan_title'  => $plan->translation->title ?? null,
                'sub_total'   => isset($planPriceConverted) ? number_format((float)$planPriceConverted, 2)  . ' ' . $symbol  : null,
                'fees'        => isset($feesConverted) ? number_format((float)$feesConverted, 2)  . ' ' . $symbol  : null,
                'commission'  => isset($commissionConverted) ? number_format((float)$commissionConverted, 2) . ' ' . $symbol  : null,
                'total'       => isset($total) ? number_format((float)$total, 2) . ' ' . $symbol : null,
            ],
        ]);
    }
}
