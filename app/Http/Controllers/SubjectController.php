<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('accommodations:id,name')->get();
        $accommodations = Accommodation::orderBy('name')->get(['id', 'name']);
        $title = "Subjects";
        return view('subjects', compact('subjects', 'accommodations', 'title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subjects,name|max:255',
            'code' => 'nullable|string|unique:subjects,code|max:255',
            'accommodation_ids' => 'nullable|array',
            'accommodation_ids.*' => 'exists:accommodations,id',
        ]);

        $subject = Subject::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);
        $subject->accommodations()->sync($validated['accommodation_ids'] ?? []);

        return back()->with('success', 'Subject Created Successfully!');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:255|unique:subjects,code,' . $subject->id,
            'accommodation_ids' => 'nullable|array',
            'accommodation_ids.*' => 'exists:accommodations,id',
        ]);

        $subject->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);

        $subject->accommodations()->sync($validated['accommodation_ids'] ?? []);

        return back()->with('success', 'Subject Updated Successfully!');
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return back()->with('success', 'Subject Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
