<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Professional;
use App\Models\RecordType;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RecordController extends Controller
{
    use \App\Traits\ExportsPupilData;
    
    public function extractFromFile(Request $request)
    {
        $recordTypes = RecordType::pluck('name')->implode(', ');

        $response_format_instructions = "
            record_type (type of the record, choose one of the following that best fits the record: [{$recordTypes}], or return null if none fit),
            title (record title),
            date (record date, format YYYY-MM-DD),
            reference_number (record reference number),
            description (record description),
            outcome (outcome or next steps),
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
        Gate::authorize('create-records');

        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'record_type_id' => 'required|exists:record_types,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
            'is_new_professional' => 'nullable|boolean',
            'prof_title' => 'nullable|string|max:255',
            'prof_first_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_last_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_role' => 'nullable|string|max:255',
            'prof_agency' => 'nullable|string|max:255',
            'prof_phone' => 'nullable|string|max:255',
            'prof_email' => 'nullable|email|max:255',
            'llm_attachment' => 'nullable|file',
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

        $record = Record::create($validated);

        $record->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $record->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', __(':item ":name" added successfully!', ['item' => __('Record'), 'name' => $record->title ?? Str::limit($record->description, 30)]));
    }

    public function update(Request $request, Record $record)
    {
        Gate::authorize('edit-records');

        $record->update($request->validate([
            'record_type_id' => 'required|exists:record_types,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $record->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Record'), 'name' => $record->title ?? Str::limit($record->description, 30)]));
    }

    public function destroy(Record $record)
    {
        Gate::authorize('delete-records');
        
        try {
            $record->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Record'), 'name' => $record->title ?? Str::limit($record->description, 30)]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
