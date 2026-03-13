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
        $response_format_instructions = "
            name (the diagnosis name),
            date (date diagnosed, format YYYY-MM-DD), 
            description (description of the diagnosis), 
            recommendations (recommended actions), 
            prof_title (professional's title e.g. Dr, Mr, Mrs), 
            prof_first_name (professional's first name), 
            prof_last_name (professional's last name), 
            prof_role (professional's role), 
            prof_agency (professional's agency/organisation), 
            prof_phone (professional's phone), 
            prof_email (professional's email). 
        ";

        return LlmService::extractAndRespond($request, $response_format_instructions);
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
            'llm_attachment' => 'nullable|file', // from AI box
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
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

        $diagnosis = Diagnosis::create($validated);

        if ($request->hasFile('llm_attachment')) {
            $file = $request->file('llm_attachment');
            $path = $file->store('attachments');

            $attachment = $diagnosis->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);

            if ($request->filled('llm_transcript')) {
                $attachment->transcription()->create([
                    'transcript' => $request->input('llm_transcript'),
                ]);
            }
        }

        $diagnosis->saveAttachments($request->file('additional_attachments'));

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
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));
        
        $diagnosis->saveAttachments($request->file('additional_attachments'));

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
