<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class MedicationController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $response_format_instructions = "
            name (the medication name),
            dosage (e.g. 50mg, 5ml), 
            frequency (e.g. Twice Daily, As Needed), 
            time_of_day (e.g. Morning, Night, 1:30pm), 
            administration_method (e.g. Oral, Injection), 
            start_date (date started, format YYYY-MM-DD), 
            end_date (date to end, format YYYY-MM-DD), 
            expiry_date (expiry date, format YYYY-MM-DD), 
            storage_instructions (any specific storage requirements), 
            self_administer (boolean, true if the pupil self-administers). 
        ";
        
        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

    public function store(Request $request)
    {
        Gate::authorize('create-medications');

        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'nullable|string|max:255',
            'administration_method' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expiry_date' => 'nullable|date',
            'storage_instructions' => 'nullable|string',
            'self_administer' => 'boolean',
            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]);

        $medication = Medication::create($validated);

        $medication->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $medication->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Medication Added Successfully!');
    }

    public function update(Request $request, Medication $medication)
    {
        Gate::authorize('edit-medications');

        $medication->update($request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'nullable|string|max:255',
            'administration_method' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expiry_date' => 'nullable|date',
            'storage_instructions' => 'nullable|string',
            'self_administer' => 'boolean',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $medication->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Medication Updated Successfully!');
    }

    public function destroy(Medication $medication)
    {
        Gate::authorize('delete-medications');
        
        try {
            $medication->delete();
            return back()->with('success', 'Medication Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
