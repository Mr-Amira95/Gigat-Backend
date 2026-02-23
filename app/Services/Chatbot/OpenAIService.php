<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public static function ask(string $prompt): string
    {
        $response = Http::withToken(config('openai.api_key'))->timeout(60)
            ->retry(3, 2000)
            ->post(config('openai.base_url') . '/chat/completions', [
                'model' => config('openai.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 1,
            ]);

            $usage = $response->json('usage', []);
        // dd($response);

        $pricing = [
                config('openai.model') => [
                    'input' => 0.05,
                    'output' => 0.40,
                ],
            ];

            $model = config('openai.model');
            $cost = ($usage['prompt_tokens'] * $pricing[$model]['input'] + $usage['completion_tokens'] * $pricing[$model]['output'])/1000000 ?? 0;

            Log::channel(channel: 'daily')->info('OpenAI API Usage', [
                'model' => $model,
                'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'estimated_cost_usd' => round($cost, 6),
            ]);

        return $response->json('choices.0.message.content', 'Sorry, I had trouble generating a response.');
    }
}
