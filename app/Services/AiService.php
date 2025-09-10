<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Faq;
use illuminate\Container\Attributes\Log;

class AiService
{
     protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('XAI_API_KEY');
        $this->apiUrl = env('XAI_API_URL', 'https://api.x.ai/v1/chat/completions');
    }

    public function getResponse(string $userMessage): string
    {
        // Check FAQ first
        $faq = Faq::where('question', 'like', '%' . $userMessage . '%')->first();
        if ($faq) {
            //Log::info('FAQ match found for: ' . $userMessage);
            return $faq->answer;
        }

        // Check API key
        if (!$this->apiKey) {
           // Log::warning('XAI_API_KEY is not set in .env');
            return 'API key is missing. Please contact the Registrar’s office for assistance.';
        }

        // Call Grok API
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'grok-3',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a virtual assistant for the Registrar of Persons in Kenya. Provide accurate, concise information about national ID registration, birth certificates, and related services. If unsure, say so and suggest contacting the Registrar’s office.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $userMessage,
                        ],
                    ],
                    'max_tokens' => 300,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            //Log::info('Grok API response received for: ' . $userMessage);
            return $data['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';
        } catch (RequestException $e) {
            //Log::error('Grok API Error: ' . $e->getMessage() . ' | Code: ' . $e->getCode());
            return 'Unable to connect to the AI service. Please try again or contact the Registrar’s office.';
        }
    }
}

