<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiConversation;
use App\Http\Resources\AiConversationResource;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;


class BotController extends Controller
{
    public function getMessages(Request $request)
    {
        $request->validate([
            'type' => 'required|in:faq,service',
        ]);

        $user = $request->user();

        $messages = AiConversation::with(['services'])
            ->where('user_id', $user->id)
            ->where('type', $request->type)
            ->orderBy('created_at')
            ->get();

        return $this->successResponse(__('messages.message_retrived'), AiConversationResource::collection($messages));
    }



    public function deleteMessages(Request $request)
    {
        $request->validate([
            'type' => 'required|in:faq,service',
        ]);

        $user = $request->user();

        // Delete all conversations for this user and type
        $deletedCount = AiConversation::where('user_id', $user->id)
            ->where('type', $request->type)
            ->delete();

        return $this->successResponse("{$deletedCount} messages deleted successfully.");
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type'    => 'required|in:faq,service',
        ]);

        $user = $request->user();

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'role'    => 'user',   // since it's sent by user
            'type'    => $request->type,
        ]);

        // Step 1: Get full messages history by type
        $messages = AiConversation::with(['services'])
            ->where('user_id', $user->id)
            ->where('type', $request->type)
            ->orderBy('created_at')
            ->get();

        // Step 2: Format payload
        $formattedMessages = $messages->map(function ($msg) {
            return [
                'role'        => $msg->role,
                'message'     => $msg->message,
                'service_ids' => $msg->services->pluck('id'), // Get the IDs directly
            ];
        })->toArray();

        if ($request->type === 'service') {
            array_pop($formattedMessages);
        }

        $payload = $request->type === 'faq'
            ? ['messages' => $formattedMessages]
            : ['messages' => $formattedMessages, 'new_message' => $request->message];

        $url = $request->type === 'faq'
            ? config('services.external.faq_url')
            : config('services.external.service_url');

        // Step 3: Call External API
        try {
            $apiResponse = Http::withHeaders([
                'X-API-Token' => config('services.external.api_token'),
            ])->post($url, $payload);

            if ($apiResponse->failed()) {

                return $this->errorResponse($apiResponse->json());
            }

            $responseJson = $apiResponse->json();

            $lastBotMessage = collect($responseJson['messages'] ?? [])
                ->filter(function ($item) {
                    return isset($item['role']) && $item['role'] === 'bot';
                })->last();

            if ($lastBotMessage) {
                $conversation = AiConversation::create([
                    'user_id' => $user->id,
                    'message' => $lastBotMessage['message'],
                    'role'    => 'bot',
                    'type'    => $request->type,
                ]);

                if (!empty($lastBotMessage['service_ids']) && is_array($lastBotMessage['service_ids'])) {

                    $validServiceIds = Service::whereIn('id', $lastBotMessage['service_ids'])->pluck('id')->toArray();
                    if (!empty($validServiceIds)) {
                        $conversation->services()->sync($validServiceIds);
                    }
                }
            }

            $lastMessage = AiConversation::with(['services'])
                ->where('user_id', $user->id)
                ->where('type', $request->type)
                ->latest('created_at') // orders by created_at descending
                ->first(); // gets only the first result (i.e., the latest)


            // return $this->successResponse(__('messages.message_retrived'), $lastMessage);
            return $this->successResponse(__('messages.message_retrived'),
                new AiConversationResource($lastMessage)
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
