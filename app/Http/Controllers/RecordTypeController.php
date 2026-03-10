<?php

namespace App\Http\Controllers;

use App\Models\RecordType;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class RecordTypeController extends Controller
{
    public function index()
    {
        Gate::authorize('view-record-types');

        $record_types = RecordType::all();
        $title = "Record Types";
        return view('record_types', compact('record_types', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-record-types');

        RecordType::create($request->validate([
            'name' => 'required|unique:record_types,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Record Type Created Successfully!');
    }

    public function update(Request $request, RecordType $record_type)
    {
        Gate::authorize('edit-record-types');

        $record_type->update($request->validate([
            'name' => 'required|max:255|unique:record_types,name,' . $record_type->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Record Type Updated Successfully!');
    }

    public function destroy(RecordType $record_type)
    {
        Gate::authorize('delete-record-types');
        
        try {
            $record_type->delete();
            return back()->with('success', 'Record Type Deleted Successfully!');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Cannot delete this type because records are assigned to it.');
            }
            return back()->with('error', 'Something went wrong.');
        }
    }
}
