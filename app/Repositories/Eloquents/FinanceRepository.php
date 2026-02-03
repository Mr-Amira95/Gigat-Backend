<?php

namespace App\Repositories\Eloquents;

use App\Enums\PaymentStatusEnum;
use App\Models\Finance;

use App\Repositories\Interfaces\FinanceRepositoryInterface;

class FinanceRepository implements FinanceRepositoryInterface
{
    protected $model;
    public function __construct(Finance $model)
    {
        $this->model = $model;
    }
    public function getAll()
    {
        return $this->model->with('request.user', 'request.service.user')->orderBy('id', 'DESC')->get();
    }
    public function getForFreelancer()
    {
        $userId = auth()->id();

        return $this->model
            ->whereHas('request.service', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['request.user', 'request.service.user'])  // note: 'services' plural
            ->orderBy('id', 'DESC');
    }

    public function getFreelancerFinancialRecords()
    {
        $userId = auth('api')->id();

        return $this->model
            ->whereHas('request.service', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['request.user', 'request.service.user'])
            ->orderByDesc('id')
            ->get();
    }
    public function getClientFinancialRecords()
    {
        $userId = auth('api')->id();

        return $this->model
            ->whereHas('request', fn($q) => $q->where('user_id', $userId))
            ->with(['request'])
            ->orderByDesc('id')
            ->get();
    }

    public function markAsPaid(array $ids)
    {
        return $this->model->whereIn('id', $ids)->update([
            'payment_status' => PaymentStatusEnum::PAID,
            'paid_at' => now()->toDateTimeString()
        ]);
    }

    public function getAllFiltered(array $filters)
    {
        return $this->model->with(['request.user', 'request.service.user'])
            ->when(
                $filters['client_id'] ?? null,
                fn($q, $v) =>
                $q->whereHas('request.user', fn($q2) => $q2->where('id', $v))
            )
            ->when(
                $filters['freelancer_id'] ?? null,
                fn($q, $v) =>
                $q->whereHas('request.service.user', fn($q2) => $q2->where('id', $v))
            )
            ->when(
                $filters['payment_status'] ?? null,
                fn($q, $v) =>
                $q->where('payment_status', $v)
            )
            ->when(
                $filters['paid_date_from'] ?? null,
                fn($q, $v) =>
                $q->whereDate('paid_at', '>=', $v)
            )
            ->when(
                $filters['paid_date_to'] ?? null,
                fn($q, $v) =>
                $q->whereDate('paid_at', '<=', $v)
            )

            ->latest()
            ->get();
    }
}
