<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestForm;

class TestFormController extends Controller
{
    public function index()
    {
        $test_rows = TestForm::orderBy('id', 'desc')->get();
        return view('test_form', compact('test_rows'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audioFile' => 'required|file',
        ]);

        if ($request->hasFile('audioFile')) {
            $file = $request->file('audioFile');
            $fileName = $file->getClientOriginalName();
            $targetDir = public_path('uploads');

            // Ensure directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetFile = $targetDir . '/' . $fileName;
            $file->move($targetDir, $fileName);

            // Transcription Logic
            $apiKey = env('MISTRAL_API_KEY');
            $audioPath = realpath($targetFile);
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

            $ch = curl_init("https://api.mistral.ai/v1/chat/completions");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$apiKey}",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
            ]);

            $response = curl_exec($ch);

            if ($response === false) {
                return back()->with('error', 'cURL error: ' . curl_error($ch));
            }
            curl_close($ch);

            $result = json_decode($response, true);
            $transcript = $result["choices"][0]["message"]["content"] ?? "";

            if (empty($transcript)) {
                return back()->with('error', 'No transcript text generated.');
            }

            // Structured Data Extraction Logic
            $structuredPayload = [
                "model" => "voxtral-mini-latest",
                "response_format" => ["type" => "json_object"],
                "messages" => [
                    [
                        "role" => "system",
                        "content" =>
                            "Return a JSON object with EXACTLY these keys: " .
                            "first_name, last_name, current_date, current_city. " .
                            "Do not guess. Use null if missing. " .
                            "For current_date, use the format YYYY-MM-DD."
                    ],
                    [
                        "role" => "user",
                        "content" => $transcript
                    ]
                ]
            ];

            $chStructured = curl_init("https://api.mistral.ai/v1/chat/completions");
            curl_setopt_array($chStructured, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$apiKey}",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($structuredPayload),
            ]);

            $structuredResponse = curl_exec($chStructured);

            if ($structuredResponse === false) {
                return back()->with('error', 'cURL error (structured): ' . curl_error($chStructured));
            }
            curl_close($chStructured);

            $structuredResult = json_decode($structuredResponse, true);
            $structuredDataRaw = $structuredResult["choices"][0]["message"]["content"] ?? "{}";

            $clean = preg_replace('/^```json\s*|\s*```$/', '', trim($structuredDataRaw));
            $structuredData = json_decode($clean, true);

            TestForm::create([
                'first_name' => $structuredData["first_name"] ?? 'Unknown',
                'last_name' => $structuredData["last_name"] ?? 'Unknown',
                'date' => $structuredData["current_date"] ?? now()->toDateString(),
                'city' => $structuredData["current_city"] ?? 'Unknown',
            ]);

            return back()->with('success', 'File uploaded and processed successfully.');
        }

        return back()->with('error', 'No file selected.');
    }
}
