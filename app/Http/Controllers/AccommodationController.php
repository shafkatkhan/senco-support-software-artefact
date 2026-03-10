<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class AccommodationController extends Controller
{
    public function index()
    {
        Gate::authorize('view-accommodations');
        $accommodations = Accommodation::all();
        $title = "Accommodations";
        return view('accommodations', compact('accommodations', 'title'));
    }
    
    public function store(Request $request)
    {
        Gate::authorize('create-accommodations');
        Accommodation::create($request->validate([
            'name' => 'required|unique:accommodations,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Accommodation Created Successfully!');
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        Gate::authorize('edit-accommodations');
        $accommodation->update($request->validate([
            'name' => 'required|max:255|unique:accommodations,name,' . $accommodation->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Accommodation Updated Successfully!');
    }

    public function destroy(Accommodation $accommodation)
    {
        Gate::authorize('delete-accommodations');
        try {
            $accommodation->delete();
            return back()->with('success', 'Accommodation Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
