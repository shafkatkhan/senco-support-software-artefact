<?php

namespace App\Http\Controllers;

use App\Models\PupilProgression;
use App\Models\Pupil;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class PupilProgressionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'year_group' => 'required|integer',
            'tutor_group' => 'nullable|string|max:255',
        ]);

        $validated['type'] = 'manual';

        PupilProgression::create($validated);

        return back()->with('success', __(':item ":name" added successfully!', ['item' => __('Progression'), 'name' => $validated['academic_year']]));
    }

    public function update(Request $request, PupilProgression $pupilProgression)
    {
        $validated = $request->validate([
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'year_group' => 'required|integer',
            'tutor_group' => 'nullable|string|max:255',
        ]);

        $pupilProgression->update($validated);

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Progression'), 'name' => $validated['academic_year']]));
    }

    public function destroy(PupilProgression $pupilProgression)
    {
        try {
            $pupilProgression->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Progression'), 'name' => $pupilProgression->academic_year]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }

    public function toggleAutoProgression(Request $request, Pupil $pupil)
    {
        $pupil->update([
            'auto_progression' => $request->has('auto_progression')
        ]);

        return back()->with('success', __('Auto-progression turned :status successfully!', ['status' => $request->has('auto_progression') ? __('on') : __('off')]));
    }
}
