<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::all();
        $title = "Accommodations";
        return view('accommodations', compact('accommodations', 'title'));
    }
    
    public function store(Request $request)
    {
        Accommodation::create($request->validate([
            'name' => 'required|unique:accommodations,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Accommodation Created Successfully!');
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $accommodation->update($request->validate([
            'name' => 'required|max:255|unique:accommodations,name,' . $accommodation->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Accommodation Updated Successfully!');
    }

    public function destroy(Accommodation $accommodation)
    {
        try {
            $accommodation->delete();
            return back()->with('success', 'Accommodation Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
