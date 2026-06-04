<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CallResource;
use App\Models\Block;
use App\Models\Call;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use Illuminate\Support\Str;

class CallController extends Controller
{
    private const ROLE_PUBLISHER = 1;
    private const ROLE_SUBSCRIBER = 2;

    private function generateAgoraToken($channelName, $uid, $role = 'publisher', $expireTimeInSeconds = 86400)
    {
        $appId = config('agora.app_id');
        $appCertificate = config('agora.app_certificate');

        if (!$appId || !$appCertificate) {
            throw new \Exception("Agora App ID and Certificate must be set in the configuration.");
        }

        $roleValue = $role === 'publisher'
            ? self::ROLE_PUBLISHER
            : self::ROLE_SUBSCRIBER;

        $currentTimestamp = Carbon::now('UTC')->timestamp;


        $privilegeExpireTs = $currentTimestamp + $expireTimeInSeconds;

        return RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $roleValue,
            $privilegeExpireTs
        );
    }


    // public function startCall(Request $request)
    // {
    //     $request->validate([
    //         'receiver_id' => 'required|exists:users,id',
    //     ]);

    //     $channelName = 'Call_' . Str::slug(auth()->user()->username, '_');
    //     $token = $this->generateAgoraToken($channelName, auth()->user()->id);

    //     $call = Call::create([
    //         'caller_id' => auth()->id(),
    //         'receiver_id' => $request->receiver_id,
    //         'channel_name' => $channelName,
    //     ]);

    //     $data = [
    //         'token' => $token,
    //         'call'  => new CallResource($call),
    //     ];

    //     $user = User::where('id', $request->receiver_id)->first();
    //     if ($user) {
    //         $playerIdRecord = PlayerId::where('user_id', $user->id)
    //             ->where('is_notifiable', 1)
    //             ->pluck('player_id')->toArray();


    //         if ($playerIdRecord) {
    //             $titles = [
    //                 'en' => __('messages.call_start_title', [], 'en'),
    //                 'ar' => __('messages.call_start_title', [], 'ar'),
    //             ];

    //             $messages = [
    //                 'en' => __('messages.call_start_message', ['caller_name' => auth()->user()->username], 'en'),
    //                 'ar' => __('messages.call_start_message', ['caller_name' => auth()->user()->username], 'ar'),
    //             ];

    //             $response = app(OneSignalService::class)->sendNotificationToUserCall(
    //                 $playerIdRecord,
    //                 $titles,
    //                 $messages,
    //                 'call',
    //                 $call->id
    //             );

    //             Notification::create([
    //                 'user_id'           => $user->id,
    //                 'title'             => json_encode($titles),
    //                 'body'              => json_encode($messages),
    //                 'type'              => 'call',
    //                 'type_id'           => $call->id,
    //                 'is_read'           => false,
    //                 'onesignal_id'      => $response['id'] ?? null,
    //                 'response_onesignal' => json_encode($response),
    //             ]);
    //         }
    //     }
    //     // *********************************************//



    //     return $this->successResponse(__('call started'), $data);
    // }
    public function startCall(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $callerId   = auth()->id();
        $receiverId = $request->receiver_id;

        /**
         * ------------------------------------------------------------
         * Rule 1: Caller has blocked the receiver → STOP call
         * ------------------------------------------------------------
         */
        $callerBlockedReceiver = Block::where('blocker_id', $callerId)
            ->where('blocked_id', $receiverId)
            ->exists();

        if ($callerBlockedReceiver) {
            return $this->errorResponse(
                __('messages.call_blocked_by_you'),
                403
            );
        }

        /**
         * ------------------------------------------------------------
         * Prepare call (this always happens if caller did NOT block)
         * ------------------------------------------------------------
         */
        $channelName = 'Call_' . Str::uuid();
        $token = $this->generateAgoraToken($channelName, $callerId);

        $call = Call::create([
            'caller_id'   => $callerId,
            'receiver_id' => $receiverId,
            'channel_name' => $channelName,
        ]);

        $data = [
            'token' => $token,
            'call'  => new CallResource($call),
        ];

        /**
         * ------------------------------------------------------------
         * Rule 2: Receiver has blocked the caller → SILENT BLOCK
         * ------------------------------------------------------------
         */
        $receiverBlockedCaller = Block::where('blocker_id', $receiverId)
            ->where('blocked_id', $callerId)
            ->exists();

        /**
         * ------------------------------------------------------------
         * Deliver call ONLY if receiver did NOT block caller
         * ------------------------------------------------------------
         */
        if (!$receiverBlockedCaller) {

            $user = User::find($receiverId);

            if ($user) {
                $playerIdRecord = PlayerId::where('user_id', $user->id)
                    ->where('is_notifiable', 1)
                    ->pluck('player_id')
                    ->toArray();

                if (!empty($playerIdRecord)) {

                    $titles = [
                        'en' => __('messages.call_start_title', [], 'en'),
                        'ar' => __('messages.call_start_title', [], 'ar'),
                    ];

                    $messages = [
                        'en' => __('messages.call_start_message', [
                            'caller_name' => auth()->user()->username
                        ], 'en'),
                        'ar' => __('messages.call_start_message', [
                            'caller_name' => auth()->user()->username
                        ], 'ar'),
                    ];

                    $response = app(OneSignalService::class)->sendNotificationToUserCall(
                        $playerIdRecord,
                        $titles,
                        $messages,
                        'call',
                        $call->id
                    );

                    Notification::create([
                        'user_id'            => $user->id,
                        'title'              => json_encode($titles),
                        'body'               => json_encode($messages),
                        'type'               => 'call',
                        'type_id'            => $call->id,
                        'is_read'            => false,
                        'onesignal_id'       => $response['id'] ?? null,
                        'response_onesignal' => json_encode($response),
                    ]);
                }
            }
        }

        /**
         * ------------------------------------------------------------
         * Always return success for caller (unless hard blocked)
         * ------------------------------------------------------------
         */
        return $this->successResponse(__('call started'), $data);
    }

    public function answerCall(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);

        $call = Call::findOrFail($request->call_id);

        if ($call->caller_id !== auth()->id() && $call->receiver_id !== auth()->id()) {
            return $this->errorResponse(__('unauthorized'), 403);
        }

        $call->update([
            'started_at' => now(),
        ]);

        $channelName = $call->channel_name;
        $token = $this->generateAgoraToken($channelName, auth()->user()->id);

        $data = [
            'token' => $token,
            'call'  => new CallResource($call),
        ];


        return $this->successResponse(
            __('Call accepted'),
            $data
        );
    }

    public function endCall(Request $request)
    {

        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);

        $call = Call::findOrFail($request->call_id);

        if ($call->caller_id !== auth()->id() && $call->receiver_id !== auth()->id()) {
            return $this->errorResponse(__('unauthorized'), 403);
        }

        if ($call->ended_at !== null) {
            // return $this->errorResponse(
            //     __('call_already_ended'),
            //     400
            // );

            return $this->successResponse(
                __('Call ended'),
                null
            );
        }

        $call->update([
            'ended_at' => now(),
        ]);

        $startedAt = $call->started_at ?? $call->ended_at;
        $durationInSeconds = $startedAt->diffInSeconds($call->ended_at);

        // $minutes = floor($durationInSeconds / 60);
        // $seconds = $durationInSeconds % 60;
        // $duration = $minutes . ' min ' . $seconds . ' sec';


        $hours   = intdiv($durationInSeconds, 3600);
        $minutes = intdiv($durationInSeconds % 3600, 60);
        $seconds = $durationInSeconds % 60;

        // Exactly like "00:03:20"
        $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        $callerId = $call->caller_id;
        $receiverId = $call->receiver_id;

        $userAuthId = auth()->user()->id;

        if ($userAuthId != $callerId) {
            $userAuthId  = $receiverId;
        }

        [$userIdOne, $userIdTwo] = [$callerId, $receiverId];

        if ($userIdOne > $userIdTwo) {
            [$userIdOne, $userIdTwo] = [$userIdTwo, $userIdOne];
        }

        // Retrieve the chat
        $chat = Chat::where('user_id_one', $userIdOne)
            ->where('user_id_two', $userIdTwo)
            ->first();

        if (!$chat) {
            return $this->errorResponse('No chat found between users.', 404);
        }

        $chatId = $chat->id;


        $message = ChatMessage::create([
            'chat_id'        => $chatId,
            'sender_id'      => $callerId,
            'message'        => $duration,
            'attachment_url' => null,
            'attachment_type' => 'call',
            'is_read' => true,
        ]);


        $user = User::where('id', $userAuthId)->first();
        if ($user) {
            $playerIdRecord = PlayerId::where('user_id', $user->id)
                ->where('is_notifiable', 1)
                ->pluck('player_id')->toArray();


            if ($playerIdRecord) {
                $titles = [
                    'en' => __('messages.end_call_title', [], 'en'),
                    'ar' => __('messages.end_call_title', [], 'ar'),
                ];

                $messages = [
                    'en' => __('messages.end_call_message', [], 'en'),
                    'ar' => __('messages.end_call_message', [], 'ar'),
                ];

                $response = app(OneSignalService::class)->sendNotificationToUserCall(
                    $playerIdRecord,
                    $titles,
                    $messages,
                    'end_call',
                    $call->id
                );
            }
        }
        // *********************************************//




        return $this->successResponse(
            __('Call ended'),
            ['call' => $call, 'message' => $message]
        );
    }
}
