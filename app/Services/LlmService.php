<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class LlmService
{
    /**
     * Transcribe an audio file using Mistral AI.
     *
     * @param string $audioPath Path to the audio file
     * @return string The transcribed text
     * @throws Exception
     */
    public static function transcribeAudio(string $audioPath): string
    {
        $apiKey = config('services.mistral.key');
        if (!$apiKey) {
            throw new Exception("MISTRAL_API_KEY is not set.");
        }

        $audioBase64 = base64_encode(file_get_contents($audioPath));

        $payload = [
            "model" => "voxtral-mini-latest",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "input_audio",
                            "input_audio" => $audioBase64,
                        ],
                        [
                            "type" => "text",
                            "text" => "Transcribe this audio file."
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post('https://api.mistral.ai/v1/chat/completions', $payload);

        if ($response->failed()) {
            throw new Exception('API error: ' . $response->body());
        }

        $result = $response->json();
        $transcript = $result["choices"][0]["message"]["content"] ?? "";

        if (empty($transcript)) {
            throw new Exception('No transcript text generated.');
        }

        return $transcript;
    }

    /**
     * Process a request and return a structured JSON response.
     *
     * @param string $data The content/data to process
     * @param string|null $instructions The system message/instructions to guide the response (optional)
     * @return array The decoded JSON response
     * @throws Exception
     */
    public static function processJsonRequest(string $data, ?string $instructions = null): array
    {
        $apiKey = config('services.mistral.key');
        if (!$apiKey) {
            throw new Exception("MISTRAL_API_KEY is not set.");
        }

        $messages = [];
        
        if ($instructions) {
            $messages[] = [
                "role" => "system",
                "content" => $instructions
            ];
        }

        $messages[] = [
            "role" => "user",
            "content" => $data
        ];

        $payload = [
            "model" => "mistral-medium-latest",
            "response_format" => ["type" => "json_object"],
            "messages" => $messages
        ];

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post('https://api.mistral.ai/v1/chat/completions', $payload);

        if ($response->failed()) {
            throw new Exception('API error: ' . $response->body());
        }

        $result = $response->json();
        $structuredDataRaw = $result["choices"][0]["message"]["content"] ?? "{}";

        $clean = preg_replace('/^```json\s*|\s*```$/', '', trim($structuredDataRaw));
        return json_decode($clean, true) ?? [];
    }
}
