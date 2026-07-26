<?php

namespace App\Repositories\Eloquents;

use App\Enums\RequestStatusEnum;
use App\Events\NewNotificationCount;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\Request;
use App\Models\RequestLog;
use App\Traits\PaginateTrait;
use App\Utilities\FileManager;
use App\Models\RequestLogAttachment;
use App\Models\User;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Services\OneSignalService;
use App\Utilities\GoogleTranslator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestRepository implements RequestRepositoryInterface
{
    use PaginateTrait;
    protected $model;
    protected $googleTranslator;

    public function __construct(Request $request, GoogleTranslator $googleTranslator)
    {
        $this->model = $request;
        $this->googleTranslator = $googleTranslator;
    }
    public function getAll(array $filters = [])
    {
        return $this->model
            ->when(!empty($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['created_date_from']), function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['created_date_from']);
            })
            ->when(!empty($filters['created_date_to']), function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['created_date_to']);
            })
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getByUser($perPage)
    {
        $query = $this->model->where('user_id', Auth::id())
        ->orderByRaw("
            CASE status
                WHEN 'approved' THEN 1
                WHEN 'completed' THEN 2
                WHEN 'in_progress' THEN 3
                WHEN 'pending' THEN 4
                WHEN 'confirmed' THEN 5
                WHEN 'cancelled' THEN 6
                ELSE 7
            END
        ")
        ->orderBy('id', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getByFreelancer($perPage)
    {
        $query = $this->model->whereHas('service', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->orderByRaw("
        CASE status
            WHEN 'pending' THEN 1
            WHEN 'in_progress' THEN 2
            WHEN 'approved' THEN 3
            WHEN 'completed' THEN 4
            WHEN 'confirmed' THEN 5
            WHEN 'cancelled' THEN 6
            ELSE 7
        END
    ")
    ->orderBy('id', 'desc');

        
        ;
        return $this->paginate($query, $perPage);
    }

    public function createRequest(array $data)
    {
        return $this->model->create($data);
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function getRequestDetails($id)
    {
        $userId = auth('api')->id();
        $query = $this->model->with([
            'user.languages.language',
            'service',
            'plan.features' => function ($query) use ($id) {
                $serviceId = $this->model->find($id)?->service_id;
                if ($serviceId) {
                    $query->where('service_id', $serviceId);
                }
            },
            'logs.user',
            'logs.attachments',
            'deliveries.translation',
            'deliveries.attachments',
            'feedbacks.translation',
            'feedbacks.attachments',
        ]);

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('service', fn($sq) => $sq->where('user_id', $userId));
            });
        }

        $request = $query->findOrFail($id);

        $user = Auth::guard('api')->user();
        if ($user && !$user->freelancer()->exists()) {
            $request->need_action = false;
            $request->save();
        }
        return $request;
    }


    public function addComment($data)
    {
        $userId = auth('api')->id();
        $request = $this->model
            ->where('id', $data['request_id'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('service', fn($sq) => $sq->where('user_id', $userId));
            })
            ->firstOrFail();
        $current_status = $request->status;
        $new_status = $data['status'];
        $freelancer = $request->service->user;
        $client = $request->user;

        //check if the status changed then update it and create a log record
        if ($current_status != $new_status) {
            $request->update([
                'status' => $new_status,
                'need_action' => true
            ]);

            $requestLog = $this->createRequestLog($userId, $request->id, Auth::user()->username . ' has updated the request status to ' . RequestStatusEnum::from($new_status)->label());
        }

        //all ways we should add log record including the message from the user
        $requestLog = $this->createRequestLog($userId, $request->id, $data['action']);

        //check if there are attachments, them add them related to the last created log
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $media) {
                $mediaPath = FileManager::upload('request_logs', $media);
                $fileType = FileManager::getFileTypeFromPath($mediaPath);

                $requestLog->attachments()->create([
                    'media_path' => $mediaPath,
                    'media_type' => $fileType,
                ]);
            }
        }

        // //if the client made the change then send the notification to the freelancer
        // if ($userId == $client->id) {
        //     $this->sendRequestNotification($freelancer, $new_status, $data['request_id']);
        // }
        // //the other case if the freelancer made the change then send the notification to the client
        // else {
        //     $this->sendRequestNotification($client, $new_status, $data['request_id']);
        // }
        // Determine notification type
        $notificationType = ($current_status == $new_status) ? 'comment' : 'status';

        // Send notification
        if ($userId == $client->id) {
            $this->sendRequestNotification($freelancer, $new_status, $data['request_id'], $notificationType);
        } else {
            $this->sendRequestNotification($client, $new_status, $data['request_id'], $notificationType);
        }


        return $requestLog->load('attachments');
    }

    private function sendRequestNotification($user, $new_status, $requestId, $type = 'status')
    {
        $playerIdRecord = PlayerId::where('user_id', $user->id)
            ->where('is_notifiable', 1)
            ->pluck('player_id')
            ->toArray();

        if (!$playerIdRecord) {
            return;
        }

        if ($type === 'comment') {
            // Username of sender
            $sender = auth()->user()->username;

            $titles = [
                'en' => __('messages.request_comment_title', [], 'en'),
                'ar' => __('messages.request_comment_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.request_comment_message', ['username' => $sender], 'en'),
                'ar' => __('messages.request_comment_message', ['username' => $sender], 'ar'),
            ];
        } else {
            // STATUS UPDATED MESSAGE
            $titles = [
                'en' => __('messages.request_updated_title', [], 'en'),
                'ar' => __('messages.request_updated_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.request_updated_message', ['status' => RequestStatusEnum::from($new_status)->label('en')], 'en'),
                'ar' => __('messages.request_updated_message', ['status' => RequestStatusEnum::from($new_status)->label('ar')], 'ar'),
            ];
        }

        $response = app(OneSignalService::class)->sendNotificationToUser(
            $playerIdRecord,
            $titles,
            $messages,
            'request',
            $requestId
        );

        Notification::create([
            'user_id'            => $user->id,
            'title'              => json_encode($titles),
            'body'               => json_encode($messages),
            'type'               => 'request',
            'type_id'            => $requestId,
            'is_read'            => false,
            'onesignal_id'       => $response['id'] ?? null,
            'response_onesignal' => json_encode($response),
        ]);
    }

    // private function sendRequestNotification($user, $new_status, $requestId)
    // {
    //     $playerIdRecord = PlayerId::where('user_id', $user->id)
    //         ->where('is_notifiable', 1)
    //         ->pluck('player_id')
    //         ->toArray();

    //     if (!$playerIdRecord) {
    //         return;
    //     }

    //     $titles = [
    //         'en' => __('messages.request_updated_title', [], 'en'),
    //         'ar' => __('messages.request_updated_title', [], 'ar'),
    //     ];

    //     $messages = [
    //         'en' => __('messages.request_updated_message', ['status' => $new_status], 'en'),
    //         'ar' => __('messages.request_updated_message', ['status' => $new_status], 'ar'),
    //     ];

    //     $response = null; // default in case OneSignal fails


    //     $response = app(OneSignalService::class)->sendNotificationToUser(
    //         $playerIdRecord,
    //         $titles,
    //         $messages,
    //         'request',
    //         $requestId
    //     );

    //     Notification::create([
    //         'user_id'            => $user->id,
    //         'title'              => json_encode($titles),
    //         'body'               => json_encode($messages),
    //         'type'               => 'request',
    //         'type_id'            => $requestId,
    //         'is_read'            => false,
    //         'onesignal_id'       => $response['id'] ?? null,
    //         'response_onesignal' => json_encode($response),
    //     ]);
    // }

    private function createRequestLog($userId, $requestId, $action)
    {
        // Create base log
        $requestLog = RequestLog::create([
            'user_id'    => $userId,
            'request_id' => $requestId,
        ]);

        // Store placeholder translations immediately; async job translates both languages
        foreach (['en', 'ar'] as $lang) {
            $requestLog->translations()->create([
                'language' => $lang,
                'action'   => $action,
            ]);
        }

        \App\Jobs\TranslateEntityJob::dispatch(
            \App\Models\RequestLog::class, $requestLog->id, $action, 'action'
        );

        return $requestLog;
    }

    public function confirmRequest($id)
    {
        $request = $this->model
            ->where('id', $id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();
        $request->update([
            'status' => 'confirmed'
        ]);
    }
}
