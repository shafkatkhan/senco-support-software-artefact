<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DiagnosisController extends Controller
{
    public function store(Request $request)
    {
        Diagnosis::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'professional_id' => 'nullable|exists:professionals,id',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]));

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
