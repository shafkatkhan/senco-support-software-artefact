<?php

namespace App\Http\Controllers;

use App\Models\SchoolHistory;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class SchoolHistoryController extends Controller
{
    use \App\Traits\ExportsPupilData;

    public function extractFromFile(Request $request)
    {
        $response_format_instructions = "
            school_name (school name),
            school_type (institution type, e.g. state school, grammar school, special school, private school, etc.),
            class_type (type of class),
            years_attended (number of years attended, format: number with optional decimal point),
            transition_reason (reason for transition).
        ";

        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

    public function store(Request $request)
    {
        Gate::authorize('create-school-histories');

        $schoolHistory = SchoolHistory::create($validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'school_name' => 'required|string|max:255',
            'school_type' => 'nullable|string',
            'class_type' => 'nullable|string',
            'years_attended' => 'nullable|numeric|min:0|max:999.9',
            'transition_reason' => 'nullable|string',
            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $schoolHistory->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $schoolHistory->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', __(':item ":name" added successfully!', ['item' => __('Previous School'), 'name' => $schoolHistory->school_name]));
    }

    public function update(Request $request, SchoolHistory $school_history)
    {
        Gate::authorize('edit-school-histories');

        $school_history->update($validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_type' => 'nullable|string',
            'class_type' => 'nullable|string',
            'years_attended' => 'nullable|numeric|min:0|max:999.9',
            'transition_reason' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $school_history->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Previous School'), 'name' => $school_history->school_name]));
    }

    public function destroy(SchoolHistory $school_history)
    {
        Gate::authorize('delete-school-histories');
        
        try {
            $school_history->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Previous School'), 'name' => $school_history->school_name]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
