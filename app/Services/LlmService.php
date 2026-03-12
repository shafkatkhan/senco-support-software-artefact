<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
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

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->attach('file', file_get_contents($audioPath), basename($audioPath))
            ->post('https://api.mistral.ai/v1/audio/transcriptions', [
                'model' => 'voxtral-mini-latest',
            ]);

        if ($response->failed()) {
            throw new Exception('API error: ' . $response->body());
        }

        $result = $response->json();
        $transcript = $result["text"] ?? "";

        if (empty($transcript)) {
            throw new Exception('No transcript text generated.');
        }

        return $transcript;
    }

    /**
     * Process a request and return a structured JSON response.
     * Optionally sends a file as a base64-encoded document if $filePath is provided.
     *
     * @param string $data The content/data or prompt to process
     * @param string|null $instructions The system message/instructions to guide the response (optional)
     * @param string|null $filePath Path to a file to extract data from (optional)
     * @param string|null $mimeType MIME type of the file (required if $filePath is provided)
     * @return array The decoded JSON response
     * @throws Exception
     */
    public static function processRequest(string $data, ?string $instructions = null, ?string $filePath = null, ?string $mimeType = null): array
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

        if ($filePath && $mimeType) {
            $fileBase64 = base64_encode(file_get_contents($filePath));
            $dataUrl = "data:{$mimeType};base64,{$fileBase64}";

            $content = [
                [
                    "type" => "document_url",
                    "document_url" => $dataUrl,
                ],
            ];
            if ($data) {
                $content[] = [
                    "type" => "text",
                    "text" => $data,
                ];
            }
            $messages[] = [
                "role" => "user",
                "content" => $content
            ];
        } else {
            $messages[] = [
                "role" => "user",
                "content" => $data
            ];
        }

        $payload = [
            "model" => $filePath ? "mistral-small-latest" : 'mistral-medium-latest', // use small for documents
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

    /**
     * Extracts structured data from an uploaded file.
     * Handles both audio and non-audio files automatically.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file
     * @param string $response_format_instructions Description of what data to extract
     * @return array Array containing 'data' and optionally 'transcript'
     * @throws Exception
     */
    public static function extractDataFromFile(UploadedFile $file, string $response_format_instructions): array
    {
        $transcript = null;

        $mimeType = $file->getMimeType();
        $fileName = $file->getClientOriginalName();
        $targetDir = public_path('uploads');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $file->move($targetDir, $fileName);
        $fullPath = realpath($targetDir . '/' . $fileName);

        $instructions = 
            "Return a JSON object with EXACTLY these keys: " . 
            $response_format_instructions . 
            "Do not guess. Use null if missing.";

        try {
            if (str_starts_with($mimeType, 'audio/')) {
                // audio file: transcribe first, then extract from transcript
                $transcript = self::transcribeAudio($fullPath);
                $data = self::processRequest($transcript, $instructions);
            } else {
                // non-audio file: send file directly to the API
                $data = self::processRequest("", $instructions, $fullPath, $mimeType);
            }

            return [
                'transcript' => $transcript,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            // delete the file if the API fails
            @unlink($fullPath);
            throw $e;
        }
    }
}
