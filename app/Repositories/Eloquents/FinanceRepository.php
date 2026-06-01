<?php

namespace App\Repositories\Eloquents;

use App\Enums\PaymentStatusEnum;
use App\Models\Finance;

use App\Repositories\Interfaces\FinanceRepositoryInterface;
use App\Services\NoticeService;

class FinanceRepository implements FinanceRepositoryInterface
{
    protected $model;
    protected $noticeService;

    public function __construct(Finance $model, NoticeService $noticeService)
    {
        $this->model = $model;
        $this->noticeService    = $noticeService;
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

    // public function markAsPaid(array $ids)
    // {
    //     return $this->model->whereIn('id', $ids)->update([
    //         'payment_status' => PaymentStatusEnum::PAID,
    //         'paid_at' => now()->toDateTimeString()
    //     ]);
    // }

    public function markAsPaid(array $ids)
    {
        // Get finances with user_id before update
        $finances = $this->model
            ->whereIn('id', $ids)
            ->where('payment_status', '!=', PaymentStatusEnum::PAID)
            ->get();

        if ($finances->isEmpty()) {
            return false;
        }

        // Update finances
        $this->model->whereIn('id', $ids)->update([
            'payment_status' => PaymentStatusEnum::PAID,
            'paid_at' => now()
        ]);

        // Send notification to each user
        foreach ($finances as $finance) {

            $titles = [
                'en' => __('finance_paid_title', [], 'en'),
                'ar' => __('finance_paid_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('finance_paid_message', [], 'en'),
                'ar' => __('finance_paid_message', [], 'ar'),
            ];

            $this->noticeService->send(
                $finance->request->service->user_id,   // user id
                $titles,
                $messages,
                'finance',
                $finance->id,
                true
            );
        }

        return true;
    }


    public function getAllFiltered(array $filters, ?int $perPage = 50)
    {
        $query = $this->model->with(['request.user', 'request.service.user'])
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
            ->latest();

        // P2-06/PERF-05: paginate for web requests; pass null for exports (needs all rows)
        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}
