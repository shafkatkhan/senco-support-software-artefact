<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $response_format_instructions = "
            title (event title),
            date (event date, format YYYY-MM-DD),
            reference_number (event reference number),
            description (event description),
            outcome (outcome or next steps).
        ";
        
        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

    public function store(Request $request)
    {
        Gate::authorize('create-events');

        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'outcome' => 'nullable|string',
            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]);

        $event = Event::create($validated);

        $event->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $event->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Event Added Successfully!');
    }

    public function update(Request $request, Event $event)
    {
        Gate::authorize('edit-events');

        $event->update($request->validate([
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'outcome' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $event->saveAttachments($request->file('additional_attachments'));

        return back()->with('success', 'Event Updated Successfully!');
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete-events');
        
        try {
            $event->delete();
            return back()->with('success', 'Event Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
