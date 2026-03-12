<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Professional;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class DiagnosisController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();

        $instructions = "Return a JSON object with EXACTLY these keys: " .
            "name (the diagnosis name), date (date diagnosed, format YYYY-MM-DD), " .
            "description (description of the diagnosis), recommendations (recommended actions), " .
            "prof_title (professional's title e.g. Dr, Mr, Mrs), prof_first_name (professional's first name), " .
            "prof_last_name (professional's last name), prof_role (professional's role), " .
            "prof_agency (professional's agency/organisation), prof_phone (professional's phone), " .
            "prof_email (professional's email). " .
            "Do not guess. Use null if missing.";

        try {
            $transcript = null;
            $fileName = $file->getClientOriginalName();
            $targetDir = public_path('uploads');
            $file->move($targetDir, $fileName);
            $fullPath = realpath($targetDir . '/' . $fileName);

            if (str_starts_with($mimeType, 'audio/')) {
                // audio file: transcribe first, then extract from transcript
                $transcript = LlmService::transcribeAudio($fullPath);
                $data = LlmService::processRequest($transcript, $instructions);
            } else {
                // non-audio file: send file directly to the API
                $data = LlmService::processRequest("", $instructions, $fullPath, $mimeType);
            }

            // delete file
            // @unlink($fullPath);

            return response()->json([
                'success' => true,
                'transcript' => $transcript,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function store(Request $request)
    {
        Gate::authorize('create-diagnoses');

        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'professional_id' => 'nullable|exists:professionals,id',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'is_new_professional' => 'nullable|boolean',
            'prof_title' => 'nullable|string|max:255',
            'prof_first_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_last_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_role' => 'nullable|string|max:255',
            'prof_agency' => 'nullable|string|max:255',
            'prof_phone' => 'nullable|string|max:255',
            'prof_email' => 'nullable|email|max:255',
        ]);

        if ($request->input('is_new_professional')) {
            // allow inline creation of professionals even if user is not authorised to create professionals from the professionals page
            $professional = Professional::create([
                'title' => $validated['prof_title'] ?? null,
                'first_name' => $validated['prof_first_name'],
                'last_name' => $validated['prof_last_name'],
                'role' => $validated['prof_role'] ?? null,
                'agency' => $validated['prof_agency'] ?? null,
                'phone' => $validated['prof_phone'] ?? null,
                'email' => $validated['prof_email'] ?? null,
            ]);
            $validated['professional_id'] = $professional->id;
        }

        Diagnosis::create($validated);

        return back()->with('success', 'Diagnosis Added Successfully!');
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        Gate::authorize('edit-diagnoses');

        $diagnosis->update($request->validate([
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'professional_id' => 'nullable|exists:professionals,id',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]));

        return back()->with('success', 'Diagnosis Updated Successfully!');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        Gate::authorize('delete-diagnoses');

        try {
            $diagnosis->delete();
            return back()->with('success', 'Diagnosis Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
