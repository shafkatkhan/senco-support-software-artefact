<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingType;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class MeetingController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $meetingTypes = MeetingType::pluck('name')->implode(', ');

        $response_format_instructions = "
            meeting_type (type of the meeting, choose one of the following that best fits the meeting: [{$meetingTypes}], or return null if none fit),
            date (meeting date, format YYYY-MM-DD),
            title (meeting title),
            participants (meeting participants),
            discussion (discussion notes),
            recommendations (recommendations or agreed actions).
        ";

        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

    public function store(Request $request)
    {
        Gate::authorize('create-meetings');

        $meeting = Meeting::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'date' => 'nullable|date',
            'title' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'discussion' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $meeting->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $meeting->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Meeting Added Successfully!');
    }

    public function update(Request $request, Meeting $meeting)
    {
        Gate::authorize('edit-meetings');

        $meeting->update($request->validate([
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'date' => 'nullable|date',
            'title' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'discussion' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $meeting->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Meeting Updated Successfully!');
    }

    public function destroy(Meeting $meeting)
    {
        Gate::authorize('delete-meetings');
        
        try {
            $meeting->delete();
            return back()->with('success', 'Meeting Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
