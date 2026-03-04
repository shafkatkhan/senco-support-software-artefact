<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DiagnosisController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'professional_id' => 'nullable|exists:professionals,id',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'is_new_professional' => 'nullable|boolean',
            'prof_title' => 'nullable|string|max:255',
            'prof_first_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_last_name' => 'nullable|string|max:255|required_if:is_new_professional,1',
            'prof_role' => 'nullable|string|max:255',
            'prof_agency' => 'nullable|string|max:255',
            'prof_phone' => 'nullable|string|max:255',
            'prof_email' => 'nullable|email|max:255',
        ]);

        if ($request->input('is_new_professional')) {
            $professional = Professional::create([
                'title' => $validated['prof_title'] ?? null,
                'first_name' => $validated['prof_first_name'],
                'last_name' => $validated['prof_last_name'],
                'role' => $validated['prof_role'] ?? null,
                'agency' => $validated['prof_agency'] ?? null,
                'phone' => $validated['prof_phone'] ?? null,
                'email' => $validated['prof_email'] ?? null,
            ]);
            $validated['professional_id'] = $professional->id;
        }

        Diagnosis::create($validated);

        return back()->with('success', 'Diagnosis Added Successfully!');
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $diagnosis->update($request->validate([
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'professional_id' => 'nullable|exists:professionals,id',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]));

        return back()->with('success', 'Diagnosis Updated Successfully!');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        try {
            $diagnosis->delete();
            return back()->with('success', 'Diagnosis Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
