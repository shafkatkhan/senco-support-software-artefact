<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        $title = "Majors";
        return view('majors', compact('majors', 'title'));
    }

    public function store(Request $request)
    {
        Major::create($request->validate([
            'name' => 'required|unique:majors,name|max:255',
            'code' => 'nullable|string|unique:majors,code|max:255',
        ]));

        return back()->with('success', 'Major Created Successfully!');
    }

    public function update(Request $request, Major $major)
    {
        $major->update($request->validate([
            'name' => 'required|max:255|unique:majors,name,' . $major->id,
            'code' => 'nullable|string|max:255|unique:majors,code,' . $major->id,
        ]));

        return back()->with('success', 'Major Updated Successfully!');
    }

    public function destroy(Major $major)
    {
        try {
            $major->delete();
            return back()->with('success', 'Major Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
