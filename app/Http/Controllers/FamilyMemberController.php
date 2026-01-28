<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class FamilyMemberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'date|nullable',
            'relation' => 'string|max:255|nullable',
        ]);

        FamilyMember::create($request->all());

        return back()->with('success', 'Family Member Added Successfully!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'date|nullable',
            'relation' => 'string|max:255|nullable',
        ]);

        FamilyMember::findOrFail($id)->update($request->all());

        return back()->with('success', 'Family Member Updated Successfully!');
    }

    public function destroy(string $id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        
        try {
            $familyMember->delete();
            return back()->with('success', 'Family Member Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
