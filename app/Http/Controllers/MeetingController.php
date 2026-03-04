<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MeetingController extends Controller
{
    public function store(Request $request)
    {
        Meeting::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'date' => 'nullable|date',
            'title' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'discussion' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]));

        return back()->with('success', 'Meeting Added Successfully!');
    }

    public function update(Request $request, Meeting $meeting)
    {
        $meeting->update($request->validate([
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'date' => 'nullable|date',
            'title' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'discussion' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]));

        return back()->with('success', 'Meeting Updated Successfully!');
    }

    public function destroy(Meeting $meeting)
    {
        try {
            $meeting->delete();
            return back()->with('success', 'Meeting Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
