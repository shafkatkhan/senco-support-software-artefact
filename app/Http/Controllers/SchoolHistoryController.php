<?php

namespace App\Http\Controllers;

use App\Models\SchoolHistory;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class SchoolHistoryController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('create-school-histories');

        SchoolHistory::create($validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'school_name' => 'required|string|max:255',
            'school_type' => 'nullable|string',
            'class_type' => 'nullable|string',
            'years_attended' => 'nullable|numeric|min:0|max:999.9',
            'transition_reason' => 'nullable|string',
        ]));

        return back()->with('success', 'Previous School Added Successfully!');
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
        ]));

        return back()->with('success', 'Previous School Updated Successfully!');
    }

    public function destroy(SchoolHistory $school_history)
    {
        Gate::authorize('delete-school-histories');
        
        try {
            $school_history->delete();
            return back()->with('success', 'Previous School Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
