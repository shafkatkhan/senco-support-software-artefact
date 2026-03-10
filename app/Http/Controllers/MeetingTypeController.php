<?php

namespace App\Http\Controllers;

use App\Models\MeetingType;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class MeetingTypeController extends Controller
{
    public function index()
    {
        Gate::authorize('view-meeting-types');

        $meeting_types = MeetingType::all();
        $title = "Meeting Types";
        return view('meeting_types', compact('meeting_types', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-meeting-types');

        MeetingType::create($request->validate([
            'name' => 'required|unique:meeting_types,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Meeting Type Created Successfully!');
    }

    public function update(Request $request, MeetingType $meeting_type)
    {
        Gate::authorize('edit-meeting-types');

        $meeting_type->update($request->validate([
            'name' => 'required|max:255|unique:meeting_types,name,' . $meeting_type->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Meeting Type Updated Successfully!');
    }

    public function destroy(MeetingType $meeting_type)
    {
        Gate::authorize('delete-meeting-types');
        
        try {
            $meeting_type->delete();
            return back()->with('success', 'Meeting Type Deleted Successfully!');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Cannot delete this type because meetings are assigned to it.');
            }
            return back()->with('error', 'Something went wrong.');
        }
    }
}
