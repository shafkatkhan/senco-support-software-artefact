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
        $request->validate([
            'name' => 'required|unique:accommodations,name|max:255',
            'description' => 'nullable|string',
        ]);

        Accommodation::create($request->all());

        return back()->with('success', 'Accommodation Created Successfully!');
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $request->validate([
            'name' => 'required|max:255|unique:accommodations,name,' . $accommodation->id,
            'detail' => 'nullable|string',
        ]);

        $accommodation->update($request->all());

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
