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

        return back()->with('success', 'Progression added successfully!');
    }

    public function update(Request $request, PupilProgression $pupilProgression)
    {
        $validated = $request->validate([
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'year_group' => 'required|integer',
            'tutor_group' => 'nullable|string|max:255',
        ]);

        $pupilProgression->update($validated);

        return back()->with('success', 'Progression updated successfully!');
    }

    public function destroy(PupilProgression $pupilProgression)
    {
        try {
            $pupilProgression->delete();
            return back()->with('success', 'Progression deleted successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function toggleAutoProgression(Request $request, Pupil $pupil)
    {
        $pupil->update([
            'auto_progression' => $request->has('auto_progression')
        ]);

        return back()->with('success', 'Auto-progression turned ' . ($request->has('auto_progression') ? 'on' : 'off') . ' successfully!');
    }
}
