<?php

namespace App\Http\Controllers\Api;

use App\Library\Agora;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\ConversationMessagesResource;

class ChatControllerold extends Controller
{
    protected $chatService;
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    public function startConversation(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);
        $conversation = $this->chatService->startConversation($request->receiver_id);
        return $this->successResponse(__('success'), new ConversationMessagesResource($conversation));
    }
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required',
            'message_type' => 'required'
        ]);
        $message = $this->chatService->sendMessage($data);
        return $this->successResponse(__('success'));
    }
    public function getMessages($conversationId)
    {
        $authUserId = Auth::id();
        $messages = $this->chatService->getMessagesByConversation($conversationId, $authUserId);
        return $this->successResponse(__('success'), $messages);
    }
    public function getConversations()
    {
        $authUserId = Auth::id();
        $conversations = $this->chatService->getConversations($authUserId);
        return $this->successResponse('success', ConversationResource::collection($conversations));
    }
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:spam,starred'
        ]);
        $this->chatService->updateStatus($data, $id);
        return $this->successResponse(__('success'));
    }
    public function getVoiceCallToken(Request $request)
    {
        $request->validate([
            'channel_name' => 'required|string',
            'uid' => 'required|integer',
        ]);

        try {
            $token = Agora::generateToken($request->channel_name, $request->uid);
            return $this->successResponse(__('success'), [
                'token' => $token,
                'channel' => $request->channel_name,
                'uid' => $request->uid
            ]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }



    function generateAgoraToken($channelName, $uid = 0)
    {
        $appID = config('services.agora.app_id');
        $appCertificate = config('services.agora.certificate');
        $expirationTimeInSeconds = 3600;
        $role = RtcTokenBuilder2::ROLE_PUBLISHER;

        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expirationTimeInSeconds;

        return RtcTokenBuilder2::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs,
            $privilegeExpiredTs
        );
    }
}
