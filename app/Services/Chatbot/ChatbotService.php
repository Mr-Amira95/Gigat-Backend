<?php

namespace App\Services\Chatbot;

use App\Http\Resources\AiConversationResource;
use App\Http\Resources\ServiceResource;
use App\Models\AiConversation;
use App\Services\Chatbot\OpenAIService;

class ChatbotService
{
    /**
     * Handle chatbot interaction.
     *
     * Flow:
     * 1. Detect intent, keywords, and language
     * 2. Retrieve relevant services / FAQs
     * 3. Save user message
     * 4. Load conversation history
     * 5. Build AI prompt with context
     * 6. Call OpenAI
     * 7. Store bot response
     * 8. Attach recommended services (if any)
     */

    public function respond(string $message, int $userId): array
    {
        /* ==========================================================
         * 1️⃣ Analyze user message (intent, keywords, language)
         * ========================================================== */
        $analysis = (new Analyst())->detect($message);

        $intent   = $analysis['intent'];
        $keywords = $analysis['keywords'];
        $language = $analysis['language'];
        // dd($keywords);

        /* ==========================================================
         * 2️⃣ Retrieve relevant services (only if SERVICE_INQUIRY)
         * ========================================================== */
        $services = [];
        // if (in_array($intent, ['SERVICE_INQUIRY'], $language)) {
        //     $services = (new Finder())->findService($keywords);
        //     // dd(vars: json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        // }
        if ($intent === 'SERVICE_INQUIRY') {
            $servicesCollection = (new Finder())->findService($keywords);
            $services = ServiceResource::collection($servicesCollection)->resolve();
            // dd($services);
        }
        // Retrieve relevant FAQs and general information
        $faqs = (new Finder())->findFaq($keywords, $language);
        $generals = (new Finder())->findGeneral();


        /* ==========================================================
         * 3️⃣ Store current user message in database
         * ========================================================== */
        // Determine conversation type (used for DB separation)
        $type = $intent === 'SERVICE_INQUIRY' ? 'service' : 'faq';

        AiConversation::create([
            'user_id' => $userId,
            'message' => $message,
            'role'    => 'user',
            'type'    => $type,
        ]);

        /* ==========================================================
         * 4️⃣ Load recent conversation history (last 15 messages)
         * ========================================================== */
        $history = AiConversation::with(['services'])
            ->where('user_id', $userId)
            ->where('type', $type)
            ->latest()
            ->take(15)
            ->get()
            ->reverse();


        $historyConversation = AiConversationResource::collection($history)
            ->resolve();

        /* ==========================================================
         * 5️⃣ Build OpenAI prompt with:
         *     - Conversation history
         *     - Detected intent
         *     - Services / FAQs / General info
         *     - Strict response rules
         * ========================================================== */
        $prompt = "
            You are a helpful assistant for a digital services company.

            Conversation history:
            " . json_encode($historyConversation, JSON_PRETTY_PRINT) . "

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

        /* ==========================================================
         * 6️⃣ Call OpenAI and safely decode response
         * ========================================================== */
        $aiResponse = json_decode(OpenAIService::ask($prompt), true)
            ?? ['ai_response' => 'Sorry, I had trouble generating a response.', 'services' => []];

        $botMessageText = $aiResponse['ai_response'] ?? '';
        $recommendedServices = $aiResponse['services'] ?? [];

        /* ==========================================================
         * 7️⃣ Store bot response in database
         * ========================================================== */
        $botConversation = AiConversation::create([
            'user_id' => $userId,
            'message' => $botMessageText,
            'role'    => 'bot',
            'type'    => $type,
        ]);

        /* ==========================================================
         * 8️⃣ Attach recommended services to conversation (if exist)
         * ========================================================== */
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
        /* ==========================================================
         * 9️⃣ Return AI response to controller
         * ========================================================== */
        return $aiResponse;
    }
}
