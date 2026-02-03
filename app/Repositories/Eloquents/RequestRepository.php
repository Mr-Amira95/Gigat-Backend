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
use Termwind\Components\Dd;

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
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getByUser($perPage)
    {
        $query = $this->model->where('user_id', Auth::id())->orderBy('id', 'desc');
        return $this->paginate($query, $perPage);
    }

    public function getByFreelancer($perPage)
    {
        $query = $this->model->whereHas('service', function ($q) {
            $q->where('user_id', Auth::id());
        });
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
        $request = $this->model->with([
            'user.languages.language',
            'service',
            'plan.features' => function ($query) use ($id) {
                $serviceId = $this->model->find($id)?->service_id;
                if ($serviceId) {
                    $query->where('service_id', $serviceId);
                }
            },
            'logs.user',
            'logs.attachments'
        ])->findOrFail($id);

        $user = Auth::guard('api')->user();
        if ($user && !$user->freelancer()->exists()) {
            $request->need_action = false;
            $request->save();
        }
        return $request;
    }


    public function addComment($data)
    {
        $request = $this->find($data['request_id']);
        $current_status = $request->status;
        $new_status = $data['status'];
        $userId = auth()->id();
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

        // Translate action text (en + ar)
        $translations = $this->googleTranslator->translateForStorage($action);

        // Store translations
        foreach ($translations as $lang => $text) {
            $requestLog->translations()->create([
                'language' => $lang,
                'action'   => $text,
            ]);
        }

        return $requestLog;
    }

    public function confirmRequest($id)
    {
        $request = $this->find($id);
        $request->update([
            'status' => 'confirmed'
        ]);
    }
}
