<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        $title = "Subjects";
        return view('subjects', compact('subjects', 'title'));
    }

    public function store(Request $request)
    {
        Subject::create($request->validate([
            'name' => 'required|unique:subjects,name|max:255',
            'code' => 'nullable|string|unique:subjects,code|max:255',
        ]));

        return back()->with('success', 'Subject Created Successfully!');
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate([
            'name' => 'required|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:255|unique:subjects,code,' . $subject->id,
        ]));

        return back()->with('success', 'Subject Updated Successfully!');
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return back()->with('success', 'Subject Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
