<?php

namespace App\Services\Chatbot;

use App\Http\Resources\AiConversationResource;
use App\Http\Resources\ServiceResource;
use App\Models\AiConversation;
use App\Services\Chatbot\OpenAIService;

class ChatbotService
{
    public function respond(string $message, int $userId): int
    {
        $analysis = (new Analyst())->detect($message);

        $intent   = $analysis['intent'];
        $keywords = $analysis['keywords'];
        $language = $analysis['language'];

        $services = [];
        if ($intent === 'SERVICE_INQUIRY') {
            $servicesCollection = (new Finder())->findService($keywords);
            $services = ServiceResource::collection($servicesCollection)->resolve();
        }

        $faqs = (new Finder())->findFaq($keywords, $language);
        $generals = (new Finder())->findGeneral();


        AiConversation::create([
            'user_id' => $userId,
            'message' => $message,
            'role'    => 'user',
        ]);

        $history = AiConversation::with(['services'])
            ->where('user_id', $userId)
            ->latest()
            ->take(15)
            ->get()
            ->reverse();

        $historyConversation = AiConversationResource::collection($history)
            ->resolve();

        $prompt = "
            You are a helpful assistant for a digital services company.

            Conversation history:
            " . json_encode($historyConversation, JSON_PRETTY_PRINT) . "
            Don't send the previous services again in the response, only use them for context.

            Detected intent:
            {$intent}

            Detected language:
            {$language}

            Rules for language:
            - Always respond in detected language (even if the user’s message is partially ambiguous).
            - Fallback if language is not recognized: respond in English politely.
            - Translation safety: clarify that service names, plan names, and FAQ titles should not be translated, only the assistant’s message text.

            Available services:
            " . json_encode($services, JSON_PRETTY_PRINT) . "
            Note:
                Each service can have multiple plans.
                - `price_from` and `price_to` indicate the range of prices across plans (cheapest to most expensive).
                - `delivery_days_from` and `delivery_days_to` indicate the minimum and maximum delivery times across plans.
                - `revisions_from` and `revisions_to` indicate the range of revisions allowed across plans.

            Review these services for relevance to the user's message and intent. If any match, and meet the user's budget, delivery time, or revision requirements (if specified), select the top three most suitable options and recommend them in your response.

            Relevant FAQs:
            " . json_encode($faqs, JSON_PRETTY_PRINT) . "

            Gemeral information:
            " . json_encode($generals, JSON_PRETTY_PRINT) . "

            If Relevant FAQs are generic, summarize the most relevant ones and ask the user to clarify.
            If the intent is SERVICE_INQUIRY, recommend the most relevant services based on the keywords and ask if they want to know more about any of them.
            If the intent is GENERAL_QUESTION, provide a concise answer based on the relevant FAQs and general information.
            If you don't have enough information to answer, ask a clarifying question to better understand the user's needs.
            Return the recommended services objects also with the message.

            Rules:
            - Use ONLY the provided services
            - If none are relevant, ask a clarifying question
            - Be concise and friendly
            - Example when services match:
            {
                \"ai_response\": \"Yes, we have mobile development services available!\",
                \"services\": [ /* relevant service objects */ ]
            }
            - Example when no services match:
            {
                \"ai_response\": \"Our company is located at **123 Main Street, City, Country**. If you have any other questions or need further information, feel free to ask!\",
                \"services\": []
            }
            ";

        $aiResponse = json_decode(OpenAIService::ask($prompt), true)
            ?? ['ai_response' => 'Sorry, I had trouble generating a response.', 'services' => []];

        $botMessageText = $aiResponse['ai_response'] ?? '';
        $recommendedServices = $aiResponse['services'] ?? [];

        $botConversation = AiConversation::create([
            'user_id' => $userId,
            'message' => $botMessageText,
            'role'    => 'bot',
        ]);

        if (!empty($recommendedServices) && is_array($recommendedServices)) {
            // Extract service IDs from AI response
            $serviceIds = collect($recommendedServices)
                ->pluck('id')
                ->toArray();

            // Validate IDs exist in database
            $validIds = \App\Models\Service::whereIn('id', $serviceIds)
                ->pluck('id')
                ->toArray();

            // Attach to pivot table
            if (!empty($validIds)) {
                $botConversation->services()->sync($validIds);
            }
        }

        return $botConversation->id;
    }
}
