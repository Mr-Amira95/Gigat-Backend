<?php

namespace App\Http\Controllers\Freelancer;

use App\Events\PusherNewMessage;
use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use Illuminate\Support\Facades\Log;

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

        $currentTimestamp = Carbon::now('UTC')->addHours(-3)->timestamp;;


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


    public function startCall($receiverId)
    {
        $receiver = User::findOrFail($receiverId);
        $channelName = 'Call_' . Str::slug(auth()->user()->username, '_');

        $call = Call::create([
            'caller_id' => auth()->id(),
            'receiver_id' => $receiverId,
            'channel_name' => $channelName,
        ]);

        $token = $this->generateAgoraToken($channelName, auth()->user()->id);


        $user = User::where('id', $receiverId)->first();

        if ($user) {
            $playerIdRecord = PlayerId::where('user_id', $user->id)
                ->where('is_notifiable', 1)
                ->pluck('player_id')->toArray();


            if ($playerIdRecord) {
                $titles = [
                    'en' => __('messages.call_start_title', [], 'en'),
                    'ar' => __('messages.call_start_title', [], 'ar'),
                ];

                $messages = [
                    'en' => __('messages.call_start_message', ['caller_name' => auth()->user()->username], 'en'),
                    'ar' => __('messages.call_start_message', ['caller_name' => auth()->user()->username], 'ar'),
                ];

                $response = app(OneSignalService::class)->sendNotificationToUserCall(
                    $playerIdRecord,
                    $titles,
                    $messages,
                    'call',
                    $call->id
                );

                Notification::create([
                    'user_id'           => $user->id,
                    'title'             => json_encode($titles),
                    'body'              => json_encode($messages),
                    'type'              => 'call',
                    'type_id'           => $call->id,
                    'is_read'           => false,
                    'onesignal_id'      => $response['id'] ?? null,
                    'response_onesignal' => json_encode($response),
                ]);
            }
        }
        // *********************************************//


        return view('pages-freelancer.call.caller', [
            'call' => $call,
            'receiver' => $receiver,
            'caller' => $call->caller,
            'channelName' => $channelName,
            'token' => $token,
            'appId' => config('agora.app_id'),
            'uid' => auth()->user()->id,
        ]);
    }

    public function answerCall($callId)
    {
        $call = Call::findOrFail($callId);
        $call->update(['started_at' => now()]);



        $channelName = $call->channel_name;
        $token = $this->generateAgoraToken($channelName, auth()->user()->id);

        if ($call->ended_at) {
            return redirect()->back()->with('error', __('This call has already ended.'));
        }
        return view('pages-freelancer.call.receiver', [
            'call' => $call,
            'receiver' => $call->receiver,
            'caller' => $call->caller,
            'token' => $token,
            'channelName' => $channelName,
            'appId' => config('agora.app_id'),
            'uid' => auth()->user()->id,
        ]);
    }

    public function endCall($callId)
    {
        $call = Call::findOrFail($callId);
        $call->update(['ended_at' => now()]);


        $startedAt = $call->started_at ?? $call->ended_at;
        $durationInSeconds = $startedAt->diffInSeconds($call->ended_at);

        $minutes = floor($durationInSeconds / 60);
        $seconds = $durationInSeconds % 60;
        $duration = $minutes . ' min ' . $seconds . ' sec';


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
        broadcast(new PusherNewMessage($message))->toOthers();


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

                // $response = app(OneSignalService::class)->sendNotificationToUserCall(
                //     $playerIdRecord,
                //     $titles,
                //     $messages,
                //     'end_call',
                //     $call->id
                // );
            }
        }
        // *********************************************//



        return redirect()->route('freelancer.home.index')->with('success', 'Call ended.');
    }




    public function status($callId)
    {
        Log::info("Checking call status for Call ID: $callId");

        $call = Call::find($callId);

        if (!$call) {
            Log::info("Call ID: $callId not found. Assuming ended.");
            return response()->json(['ended' => true]);
        }

        $ended = $call->ended_at !== null;

        Log::info("Call ID: $callId ended status: " . ($ended ? 'true' : 'false'));

        return response()->json(['ended' => $ended]);
    }
}
