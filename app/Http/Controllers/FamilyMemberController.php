<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class FamilyMemberController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $response_format_instructions = "
            first_name (family member first name),
            last_name (family member last name),
            dob (date of birth, format YYYY-MM-DD),
            relation (relationship to the pupil),
            phone (phone number),
            email (email address),
            address_line_1 (address line 1),
            address_line_2 (address line 2),
            locality (town or city),
            postcode (postcode),
            country (country),
            marital_status (marital status),
            highest_education (highest level of education),
            financial_status (financial status),
            occupation (occupation or job title),
            state_support (state support or benefits, e.g. jobseeker's allowance, disability benefits, universal credit, etc.),
            next_of_kin (boolean, true if this is the pupil's next of kin).
        ";

        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

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
            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $familyMember->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
        $familyMember->saveAttachments($request->file('additional_attachments'));

        if ($request->has('next_of_kin') && $request->next_of_kin) {
            $familyMember->pupil->update(['primary_family_member_id' => $familyMember->id]);
        }

        return back()->with('success', __(':item ":name" added successfully!', ['item' => __('Family Member'), 'name' => $familyMember->first_name.' '.$familyMember->last_name]));
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
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]));

        $family_member->saveAttachments($request->file('additional_attachments'));

        if ($request->input('next_of_kin') == '1') {
            // set as primary family member
            $family_member->pupil->update(['primary_family_member_id' => $family_member->id]);
        } elseif ($family_member->pupil->primary_family_member_id == $family_member->id) {
            // unset as primary family member
            $family_member->pupil->update(['primary_family_member_id' => null]);
        }

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('Family Member'), 'name' => $family_member->first_name.' '.$family_member->last_name]));
    }

    public function destroy(FamilyMember $family_member)
    {
        Gate::authorize('delete-family-members');
        
        try {
            $family_member->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('Family Member'), 'name' => $family_member->first_name.' '.$family_member->last_name]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
