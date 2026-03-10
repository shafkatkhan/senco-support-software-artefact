<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
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
        ]);

        Event::create($validated);

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
        ]));

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
