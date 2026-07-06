<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AiConversationResource;
use App\Models\AiConversation;
use App\Services\Chatbot\ChatbotService;
use App\Services\MetaConversionsApiService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    // public function chatbot(Request $request)
    // {
    //     $request->validate([
    //         'message' => 'required|string|max:1000'
    //     ]);

    //     $reply = (new ChatbotService())->respond($request->message);

    //     return response()->json([
    //         'reply' => $reply
    //     ]);
    // }

    public function chatbot(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = $request->user();

        $isNewConversation = AiConversation::where('user_id', $user->id)->doesntExist();

        $chatbotService = new ChatbotService();

        $botConversation = $chatbotService->respond(
            message: $request->message,
            userId: $user->id
        );

        if ($isNewConversation && !$user->freelancer) {
            app(MetaConversionsApiService::class)->dispatchEvent($user, $request, 'Client Start AI Conversation');
        }

        return response()->json(
            [
                'reply' => $botConversation
            ]
        );

    }
}
