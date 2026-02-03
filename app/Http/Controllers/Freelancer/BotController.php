<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'faq');

        $userId = auth()->user()->id;

        $messages = AiConversation::with(['services'])
            ->where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('created_at')
            ->get();

        return view('pages-freelancer.ai.index', compact('messages', 'type'));
    }

    public function deleteMessages(Request $request)
    {
        $request->validate([
            'type' => 'required|in:faq,service',
        ]);

        $userId = auth()->user()->id;

        $deletedCount = AiConversation::where('user_id', $userId)
            ->where('type', $request->type)
            ->delete();


        return redirect()->route('freelancer.ai.index', ['type' => $request->type])
            ->with('success', "{$deletedCount} message(s) deleted successfully.");
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type'    => 'required|in:faq,service',
        ]);

        $user = auth()->user();

        // Save user message
        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'role'    => 'user',
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
                'service_ids' => $msg->services->pluck('id'),
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
                return redirect()->back()->with('error', 'Failed to connect to AI service.');
            }

            $responseJson = $apiResponse->json();

            $lastBotMessage = collect($responseJson['messages'] ?? [])
                ->filter(function ($item) {
                    return isset($item['role']) && $item['role'] === 'bot';
                })->last();

            if ($lastBotMessage) {
                AiConversation::create([
                    'user_id' => $user->id,
                    'message' => $lastBotMessage['message'],
                    'role'    => 'bot',
                    'type'    => $request->type,
                ]);
            }
            return response()->json([
                'status'  => 'success',
                'message' => $lastBotMessage['message'] ?? 'No response from AI.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
