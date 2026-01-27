<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;

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

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:accommodations,name,' . $id,
            'detail' => 'nullable|string',
        ]);

        $accommodation = Accommodation::findOrFail($id);
        $accommodation->update($request->all());

        return back()->with('success', 'Accommodation Updated Successfully!');
    }

    public function destroy(string $id)
    {
        $accommodation = Accommodation::findOrFail($id);
        $accommodation->delete();

        return back()->with('success', 'Accommodation Deleted Successfully!');
    }
}
