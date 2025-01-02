<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * Send a message to the OpenAI API and return the response.
     *
     * @param array $messages An array of message objects for the conversation.
     * @return array The response from the OpenAI API.
     * @throws \Exception If the API call fails.
     */
    public function sendMessage(array $conversationHistory): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('app.OPENAI_API_KEY'),
        ];

        try {
            $response = Http::withHeaders($headers)->post($this->apiUrl, [
                'model' => 'gpt-4o-mini',
                'store' => true,
                'messages' => $conversationHistory,
                'stream' => false,
            ]);

            if ($response->ok()) {
                return $response->json();
            }

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('OpenAI API returned an error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Failed to communicate with OpenAI API', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
