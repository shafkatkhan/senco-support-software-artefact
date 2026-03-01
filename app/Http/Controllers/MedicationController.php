<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MedicationController extends Controller
{
    public function store(Request $request)
    {
        Medication::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'name' => 'required|string|max:255',
            'dosage' => 'string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'nullable|string|max:255',
            'administration_method' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expiry_date' => 'nullable|date',
            'storage_instructions' => 'nullable|string',
            'self_administer' => 'boolean',
        ]));

        return back()->with('success', 'Medication Added Successfully!');
    }

    public function update(Request $request, Medication $medication)
    {
        $medication->update($request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'string|max:255',
            'frequency' => 'required|string|max:255',
            'time_of_day' => 'nullable|string|max:255',
            'administration_method' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expiry_date' => 'nullable|date',
            'storage_instructions' => 'nullable|string',
            'self_administer' => 'boolean',
        ]));

        return back()->with('success', 'Medication Updated Successfully!');
    }

    public function destroy(Medication $medication)
    {
        try {
            $medication->delete();
            return back()->with('success', 'Medication Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
