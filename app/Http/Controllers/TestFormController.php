<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LlmService;
use App\Models\TestForm;

class TestFormController extends Controller
{
    public function index()
    {
        $test_rows = TestForm::orderBy('id', 'desc')->get();
        $title = 'Test Form';
        return view('test_form', compact('test_rows', 'title'));
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

            try {
                // transcription logic
                $transcript = LlmService::transcribeAudio(realpath($targetFile));

                // structured data extraction logic
                $systemPrompt = "Return a JSON object with EXACTLY these keys: " .
                    "first_name, last_name, current_date, current_city. " .
                    "Do not guess. Use null if missing. " .
                    "For current_date, use the format YYYY-MM-DD.";

                $structuredData = LlmService::processRequest($transcript, $systemPrompt);
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }

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
