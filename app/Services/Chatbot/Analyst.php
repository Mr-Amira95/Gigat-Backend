<?php

namespace App\Services\Chatbot;

use App\Services\Chatbot\OpenAIService;

class Analyst
{
    public function detect(string $message): array
    {
$prompt = "
Classify the user's message into one of two intents: GENERAL_QUESTION or SERVICE_INQUIRY.
Classify the user's message language one of two intents: ar, en, or any other language

Rules:
- GENERAL_QUESTION: general inquiries about the company, policies, or information not directly related to purchasing a service.
- SERVICE_INQUIRY: asking specifically about a service, pricing, features, or making a request to use a service.
- Generate relevant keywords for database searching. Only provide keywords in ar or en. If the text is in another language, translate it into English before generating the keywords
- Remove filler words (i, want, looking, for, etc.)
- Max 10 keywords
- JSON ONLY, no explanation

Examples:
Message: 'What payment methods do you accept?'
Response: {\"intent\": \"GENERAL_QUESTION\", \"language\": \"en\", \"keywords\": [\"payment\", \"methods\"]}

Message: 'I want a mobile app developer'
Response: {\"intent\": \"SERVICE_INQUIRY\", \"intent\": \"en\", \"keywords\": [\"mobile\", \"app\", \"developer\"]}

Message: 'Can you help me find graphic desigener to create a logo'
Response: {\"intent\": \"SERVICE_INQUIRY\", \"intent\": \"en\", \"keywords\": [\"mobile\", \"app\", \"developer\"]}

Message: 'Do you provide technical support?'
Response: {\"intent\": \"GENERAL_QUESTION\", \"intent\": \"en\", \"keywords\": [\"technical\", \"support\"]}

Message: 'How can I subscribe to your services?'
Response: {\"intent\": \"GENERAL_QUESTION\", \"intent\": \"en\", \"keywords\": [\"subscribe\", \"services\"]}

User message:
\"{$message}\"
";

    $response = OpenAIService::ask($prompt);

    $clean = preg_replace('/```json|```/i', '', $response);
    $clean = trim($clean);

    $data = json_decode($clean, true);

        return $data ?? [
            'intent' => 'GENERAL_QUESTION',
            'language' => 'English',
            'keywords' => []
        ];
    }
}