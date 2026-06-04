<?php

namespace App\Services\Chatbot;

use App\Services\Chatbot\OpenAIService;

class Analyst
{
    public function detect(string $message): array
    {
        // P2-12/SEC-17: Sanitize user input before interpolation to prevent prompt injection.
        // Strip role-boundary markers that could override system instructions.
        $safeMessage = $this->sanitizeForPrompt($message);

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
\"{$safeMessage}\"
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

    private function sanitizeForPrompt(string $input): string
    {
        // Remove common prompt-injection boundary tokens that could override system instructions
        $dangerous = ['system:', 'assistant:', 'user:', 'SYSTEM:', 'ASSISTANT:', 'USER:',
                      '###', '---', '```', '<|im_start|>', '<|im_end|>', '<|endoftext|>'];
        $sanitized = str_replace($dangerous, '', $input);
        // Truncate to prevent excessively long inputs from consuming token budget
        return mb_substr(trim($sanitized), 0, 1000);
    }
}
