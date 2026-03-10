<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class FamilyMemberController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('create-family-members');

        $familyMember = FamilyMember::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'date|nullable',
            'relation' => 'string|max:255|nullable',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'locality' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'financial_status' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'state_support' => 'nullable|string|max:255',
        ]));

        if ($request->has('next_of_kin') && $request->next_of_kin) {
            $familyMember->pupil->update(['primary_family_member_id' => $familyMember->id]);
        }

        return back()->with('success', 'Family Member Added Successfully!');
    }

    public function update(Request $request, FamilyMember $family_member)
    {
        Gate::authorize('edit-family-members');

        $family_member->update($request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'date|nullable',
            'relation' => 'string|max:255|nullable',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'locality' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'financial_status' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'state_support' => 'nullable|string|max:255',
        ]));

        if ($request->input('next_of_kin') == '1') {
            // set as primary family member
            $family_member->pupil->update(['primary_family_member_id' => $family_member->id]);
        } elseif ($family_member->pupil->primary_family_member_id == $family_member->id) {
            // unset as primary family member
            $family_member->pupil->update(['primary_family_member_id' => null]);
        }

        return back()->with('success', 'Family Member Updated Successfully!');
    }

    public function destroy(FamilyMember $family_member)
    {
        Gate::authorize('delete-family-members');
        
        try {
            $family_member->delete();
            return back()->with('success', 'Family Member Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
