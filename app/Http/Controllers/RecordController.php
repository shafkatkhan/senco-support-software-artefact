<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'record_type_id' => 'required|exists:record_types,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        Record::create($request->all());

        return back()->with('success', 'Record Added Successfully!');
    }

    public function update(Request $request, Record $record)
    {
        $request->validate([
            'record_type_id' => 'required|exists:record_types,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        $record->update($request->all());

        return back()->with('success', 'Record Updated Successfully!');
    }

    public function destroy(Record $record)
    {
        try {
            $record->delete();
            return back()->with('success', 'Record Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
