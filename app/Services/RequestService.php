<?php

namespace App\Services;

use App\Models\Finance;
use App\Models\General;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Services\ServiceService;
use Carbon\Carbon;

class RequestService
{
    protected $requestRepository;
    protected $serviceService;
    public function __construct(RequestRepositoryInterface $requestRepository, ServiceService $serviceService)
    {
        $this->requestRepository = $requestRepository;
        $this->serviceService = $serviceService;
    }
    public function getAll(array $filters = [])
    {
        return $this->requestRepository->getAll($filters);
    }
    public function getByUser($perPage = null)
    {
        return $this->requestRepository->getByUser($perPage);
    }
    public function getByFreelancer($perPage = null)
    {
        return $this->requestRepository->getByFreelancer($perPage);
    }

    public function createRequest(array $data)
    {
        $service = $this->serviceService->getServiceById($data['service_id']);
        $data['user_id'] = auth()->guard('api')->user()->id ?? $data['user_id'];
        $data['order_number'] = '#' . mt_rand(100000, 999999);
        // $data['title'] = $service->translation->title;
        $data['image'] = $service->media->where('is_cover', true)->first()->media_path ??  null;

        // Load plan with features + translations
        $plan = Plan::where('id', $data['plan_id'])
            ->with(['features' => function ($query) use ($data) {
                $query->where('service_id', $data['service_id']);
            }, 'features.translations'])
            ->first();

        // Delivery days
        $deliveryDaysValue = intval(optional($plan->features->where('type', 'delivery_days')->first())->value ?? 0);
        $data['start_date'] = now();
        $data['end_date'] = Carbon::now()->addDays($deliveryDaysValue)->toDateString();


        // Get price + revisions
        $priceFeature = $plan->features->where('type', 'price')->first();
        // $amount = floatval(optional($priceFeature)->value ?? 0);
        $rawAmount = optional($priceFeature)->value ?? 0;
        $amount = floatval(str_replace(',', '', $rawAmount));

        $revisionFeature = $plan->features->where('type', 'revisions')->first()->value;

        // Finance calculations
        $feesValue = floatval(optional(General::where('key', 'fees')->first())->value ?? 0);
        $commissionValue = floatval(optional(General::where('key', 'commission')->first())->value ?? 0);
        $feesAmount = ($amount * $feesValue) / 100;
        $commissionAmount = ($amount * $commissionValue) / 100;
        $discount = 0;
        $total = $amount + $feesAmount - $discount;

        // Create request record
        $request = $this->requestRepository->createRequest($data);

        // Copy service translations
        foreach ($service->translations as $translation) {
            $request->translations()->create([
                'language' => $translation->language,
                'title'    => $translation->title,
                'description'    => $translation->description,
            ]);
        }

        // Copy plan features + translations
        foreach ($plan->features as $feature) {
            $requestFeature = $request->features()->create([
                'plan_id'    => $plan->id,
                'request_id' => $request->id,
                'type'       => $feature->type,
                'value'      => $feature->value,
            ]);

            foreach ($feature->translations as $translation) {
                $requestFeature->translations()->create([
                    'language' => $translation->language,
                    'title'    => $translation->title,
                ]);
            }
        }

        // Finance record
        $finance = Finance::create([
            'request_id'     => $request->id,
            'amount'         => $amount,
            'fees'           => $feesAmount,
            'commission'     => $commissionAmount,
            'discount'       => $discount,
            'total'          => $total,
            'client_payment_status' => $data['client_payment_status'],
            'payment_status' => 'unpaid',
            'payment_method' => 'credit_card',
            'paid_at'        => null,
        ]);

        $data = [
            'request' => $request,
            'finance' => $finance,
            'revision' => $revisionFeature,
            'delivery_date' => $data['end_date']
        ];
        return  $data;
    }
    public function getRequestDetails($id)
    {
        return $this->requestRepository->getRequestDetails($id);
    }
    public function addComment($data)
    {
        return $this->requestRepository->addComment($data);
    }
    public function confirmRequest($id)
    {
        return $this->requestRepository->confirmRequest($id);
    }
}
