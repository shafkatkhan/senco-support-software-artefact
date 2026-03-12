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

            try {
                // structured data extraction logic
                $response_format_instructions = "
                    first_name (the first name of the person),
                    last_name (the last name of the person),
                    current_date (the date of the recording, format YYYY-MM-DD),
                    current_city (the city where the recording was made)
                ";

                $extraction = LlmService::extractDataFromFile($file, $response_format_instructions);
                $structuredData = $extraction['data'];
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
