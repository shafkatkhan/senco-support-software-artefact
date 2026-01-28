<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DiagnosisController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'carried_out_by' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        Diagnosis::create($request->all());

        return back()->with('success', 'Diagnosis Added Successfully!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'carried_out_by' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        Diagnosis::findOrFail($id)->update($request->all());

        return back()->with('success', 'Diagnosis Updated Successfully!');
    }

    public function destroy(string $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);
        
        try {
            $diagnosis->delete();
            return back()->with('success', 'Diagnosis Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
