<?php

namespace App\Console\Commands;

use App\Models\Request;
use App\Services\NoticeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyRequestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:daily-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily request notifications at 10 AM';

    protected NoticeService $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        parent::__construct();
        $this->noticeService = $noticeService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('🔔 Daily Request Notification Job Started');

        try {

            $today = now()->toDateString();

            // 1️⃣ Pending
            $pendingRequests = Request::where('status', 'pending')->get();
            foreach ($pendingRequests as $request) {
                $orderNumber = $request->order_number ?? 'Unknown';

                $this->noticeService->send(
                    $request->service->user_id,
                    [
                        'en' => __('messages.daily_pending_title', [], 'en'),
                        'ar' => __('messages.daily_pending_title', [], 'ar'),
                    ],
                    [
                        'en' => __('messages.daily_pending_message', [
                            'order_number' => $orderNumber
                        ], 'en'),
                        'ar' => __('messages.daily_pending_message', [
                            'order_number' => $orderNumber
                        ], 'ar'),
                    ],
                    'request',
                    $request->id
                );
            }

            // 2️⃣ Due Today
            $dueRequests = Request::whereDate('end_date', $today)
                ->where('status', 'in_progress')
                ->get();

            foreach ($dueRequests as $request) {
                $orderNumber = $request->order_number ?? 'Unknown';

                $this->noticeService->send(
                    $request->service->user_id,
                    [
                        'en' => __('messages.daily_due_title', [], 'en'),
                        'ar' => __('messages.daily_due_title', [], 'ar'),
                    ],
                    [
                        'en' => __('messages.daily_due_message', [
                            'order_number' => $orderNumber
                        ], 'en'),
                        'ar' => __('messages.daily_due_message', [
                            'order_number' => $orderNumber
                        ], 'ar'),
                    ],
                    'request',
                    $request->id
                );
            }

            // 3️⃣ Completed
            $completedRequests = Request::where('status', 'completed')->get();

            foreach ($completedRequests as $request) {
                $orderNumber = $request->order_number ?? 'Unknown';

                $this->noticeService->send(
                    $request->user_id,
                    [
                        'en' => __('messages.daily_completed_title', [], 'en'),
                        'ar' => __('messages.daily_completed_title', [], 'ar'),
                    ],
                    [
                        'en' => __('messages.daily_completed_message', [
                            'order_number' => $orderNumber
                        ], 'en'),
                        'ar' => __('messages.daily_completed_message', [
                            'order_number' => $orderNumber
                        ], 'ar'),
                    ],
                    'request',
                    $request->id
                );
            }

            Log::info('✅ Daily Request Notification Job Finished Successfully');
            $this->info('Daily request notifications sent successfully.');
        } catch (\Exception $e) {

            Log::error('❌ Daily Request Notification Job Failed: ' . $e->getMessage());
        }
    }
}
