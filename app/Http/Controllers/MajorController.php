<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::with('subjects:id,name')->get();
        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        $title = "Majors";
        return view('majors', compact('majors', 'subjects', 'title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:majors,name|max:255',
            'code' => 'nullable|string|unique:majors,code|max:255',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $major = Major::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);
        $major->subjects()->sync($validated['subject_ids'] ?? []);

        return back()->with('success', 'Major Created Successfully!');
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:majors,name,' . $major->id,
            'code' => 'nullable|string|max:255|unique:majors,code,' . $major->id,
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $major->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);
        $major->subjects()->sync($validated['subject_ids'] ?? []);

        return back()->with('success', 'Major Updated Successfully!');
    }

    public function destroy(Major $major)
    {
        try {
            $major->delete();
            return back()->with('success', 'Major Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
