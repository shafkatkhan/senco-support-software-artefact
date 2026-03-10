<?php

namespace App\Http\Controllers;

use App\Models\Proficiency;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class ProficiencyController extends Controller
{
    public function index()
    {
        Gate::authorize('view-proficiencies');

        $proficiencies = Proficiency::all();
        $title = "Proficiencies";
        return view('proficiencies', compact('proficiencies', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-proficiencies');

        Proficiency::create($request->validate([
            'name' => 'required|unique:proficiencies,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Proficiency Created Successfully!');
    }

    public function update(Request $request, Proficiency $proficiency)
    {
        Gate::authorize('edit-proficiencies');

        $proficiency->update($request->validate([
            'name' => 'required|max:255|unique:proficiencies,name,' . $proficiency->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Proficiency Updated Successfully!');
    }

    public function destroy(Proficiency $proficiency)
    {
        Gate::authorize('delete-proficiencies');
        
        try {
            $proficiency->delete();
            return back()->with('success', 'Proficiency Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
