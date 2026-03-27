<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use App\Models\Setting;
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
    public static function transcribeAudio(string $audioPath, string $fileName, ?string $overrideProvider = null, ?string $overrideModel = null): string
    {
        // override php time limit
        set_time_limit(500);

        $provider = $overrideProvider ?? Setting::get('llm_provider', 'mistral');

        if ($provider == 'openai') {
            $apiUrl = 'https://api.openai.com/v1/audio/transcriptions';
            $model = $overrideModel ?? 'whisper-1';
        } else {
            $apiUrl = 'https://api.mistral.ai/v1/audio/transcriptions';
            $model = $overrideModel ?? 'voxtral-mini-latest';
        }
        $apiKey = config('services.' . $provider . '.key');
        if (!$apiKey) {
            throw new Exception(strtoupper($provider) . "_API_KEY is not set.");
        }

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->attach('file', file_get_contents($audioPath), $fileName)
            ->post($apiUrl, [
                'model' => $model,
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
    public static function processRequest(string $data, ?string $instructions = null, ?string $filePath = null, ?string $mimeType = null, ?string $overrideProvider = null, ?string $overrideModel = null): array
    {
        // override php time limit
        set_time_limit(500);

        $provider = $overrideProvider ?? Setting::get('llm_provider', 'mistral');
        $isImage = $mimeType ? str_starts_with($mimeType, 'image/') : false;

        if ($provider == 'openai') {
            $apiUrl = 'https://api.openai.com/v1/chat/completions';
        } else {
            $apiUrl = 'https://api.mistral.ai/v1/chat/completions';
        }
        $apiKey = config('services.' . $provider . '.key');
        if (!$apiKey) {
            throw new Exception(strtoupper($provider) . "_API_KEY is not set.");
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
            
            $content = [];
            
            if ($provider == 'openai') {
                if ($isImage) {
                    $content[] = [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => $dataUrl
                        ],
                    ];
                } else {
                    $content[] = [
                        "type" => "file",
                        "file" => [
                            "filename" => basename($filePath),
                            "file_data" => $dataUrl
                        ]
                    ];
                }
                $model = $overrideModel ?? "gpt-4.1-nano";
            } else {
                $content[] = [
                    "type" => $isImage ? "image_url" : "document_url",
                    $isImage ? "image_url" : "document_url" => $dataUrl,
                ];
                $model = $overrideModel ?? ($isImage ? "pixtral-12b-2409" : "mistral-small-latest");
            }

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
            $model = $overrideModel ?? ($provider == 'openai' ? "gpt-4.1-nano" : "mistral-small-latest");
        }

        $payload = [
            "model" => $model,
            "response_format" => ["type" => "json_object"],
            "messages" => $messages
        ];

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post($apiUrl, $payload);

        if ($response->failed()) {
            throw new Exception('API error: ' . $response->body());
        }

        $result = $response->json();
        $structuredDataRaw = $result["choices"][0]["message"]["content"] ?? "{}";

        return json_decode($structuredDataRaw, true) ?? [];
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
        $fullPath = $file->getPathname(); // access the temporary path directly

        $instructions = 
            "Return a JSON object with EXACTLY these keys: " . 
            $response_format_instructions . 
            "Do not guess. Use null if missing.";

        if (str_starts_with($mimeType, 'audio/')) {
            // audio file: transcribe first, then extract from transcript
            $transcript = self::transcribeAudio($fullPath, $fileName);
            $data = self::processRequest($transcript, $instructions);
        } else {
            // non-audio file: send file directly to the API
            $data = self::processRequest("", $instructions, $fullPath, $mimeType);
        }

        return [
            'transcript' => $transcript,
            'data' => $data,
        ];
    }

    /**
     * Extracts structured data from an uploaded file and returns a JSON response.
     * Standardises the controller extraction process for different types of data.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $response_format_instructions
     * @return \Illuminate\Http\JsonResponse
     */
    public static function extractAndRespond(\Illuminate\Http\Request $request, string $response_format_instructions)
    {
        // override php time limit
        set_time_limit(500);

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        try {
            $extraction = self::extractDataFromFile($file, $response_format_instructions);

            return response()->json([
                'success' => true,
                'transcript' => $extraction['transcript'],
                'data' => $extraction['data'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
