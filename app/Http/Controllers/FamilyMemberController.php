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

        $familyMember = FamilyMember::create($request->all());

        if ($request->has('next_of_kin') && $request->next_of_kin) {
            $familyMember->pupil->update(['primary_family_member_id' => $familyMember->id]);
        }

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

        $familyMember = FamilyMember::findOrFail($id);
        $familyMember->update($request->all());

        if ($request->input('next_of_kin') == '1') {
            // set as primary family member
            $familyMember->pupil->update(['primary_family_member_id' => $familyMember->id]);
        } elseif ($familyMember->pupil->primary_family_member_id == $familyMember->id) {
            // unset as primary family member
            $familyMember->pupil->update(['primary_family_member_id' => null]);
        }

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
