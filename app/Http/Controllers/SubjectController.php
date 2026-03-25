<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Proficiency;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class SubjectController extends Controller
{
    public function index()
    {
        Gate::authorize('view-subjects');

        $subjects = Subject::with(['accommodations:id,name', 'proficiencies:id,name'])->get();
        $accommodations = Accommodation::orderBy('name')->get(['id', 'name']);
        $proficiencies = Proficiency::orderBy('name')->get(['id', 'name']);
        $title = __('Subjects');
        return view('subjects', compact('subjects', 'accommodations', 'proficiencies', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-subjects');

        $validated = $request->validate([
            'name' => 'required|unique:subjects,name|max:255',
            'code' => 'nullable|string|unique:subjects,code|max:255',
            'accommodation_ids' => 'nullable|array',
            'accommodation_ids.*' => 'exists:accommodations,id',
            'proficiency_ids' => 'nullable|array',
            'proficiency_ids.*' => 'exists:proficiencies,id',
        ]);

        $subject = Subject::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);
        $subject->accommodations()->sync($validated['accommodation_ids'] ?? []);
        $subject->proficiencies()->sync($validated['proficiency_ids'] ?? []);

        return back()->with('success', __(':item ":name" created successfully!', ['item' => __('Subject'), 'name' => $subject->name]));
    }

    public function update(Request $request, Subject $subject)
    {
        Gate::authorize('edit-subjects');

        $validated = $request->validate([
            'name' => 'required|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:255|unique:subjects,code,' . $subject->id,
            'accommodation_ids' => 'nullable|array',
            'accommodation_ids.*' => 'exists:accommodations,id',
            'proficiency_ids' => 'nullable|array',
            'proficiency_ids.*' => 'exists:proficiencies,id',
        ]);

        $subject->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
        ]);

        $subject->accommodations()->sync($validated['accommodation_ids'] ?? []);
        $subject->proficiencies()->sync($validated['proficiency_ids'] ?? []);

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Subject'), 'name' => $subject->name]));
    }

    public function destroy(Subject $subject)
    {
        Gate::authorize('delete-subjects');
        
        try {
            $subject->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Subject'), 'name' => $subject->name]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
