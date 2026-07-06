<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiConversation;
use App\Http\Resources\AiConversationResource;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use App\Services\Chatbot\ChatbotService;
use App\Services\MetaConversionsApiService;

class BotController extends Controller
{
    public function getMessages(Request $request)
    {

        $user = $request->user();

        $messages = AiConversation::with(['services'])
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        return $this->successResponse(__('messages.message_retrived'), AiConversationResource::collection($messages));
    }

    public function deleteMessages(Request $request)
    {
        $user = $request->user();

        $deletedCount = AiConversation::where('user_id', $user->id)->delete();

        return $this->successResponse("{$deletedCount} messages deleted successfully.");
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = $request->user();

        $isNewConversation = AiConversation::where('user_id', $user->id)->doesntExist();

        $chatbotService = new ChatbotService();

        $botConversationId = $chatbotService->respond(
            message: $request->message,
            userId: $user->id
        );

        if ($isNewConversation && !$user->freelancer) {
            app(MetaConversionsApiService::class)->dispatchEvent($user, $request, 'Client Start AI Conversation');
        }

        $message = AiConversation::with(['services'])->find($botConversationId);

        // return $this->successResponse(__('messages.message_sent'), AiConversationResource::collection($message));
        return $this->successResponse(__('messages.message_sent'), new AiConversationResource($message));
    }
}
