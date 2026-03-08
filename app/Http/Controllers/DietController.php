<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class DietController extends Controller
{
    public function store(Request $request)
    {
        Diet::create($request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('diets')->where('pupil_id', $request->pupil_id)
            ],
            'proficiency_id' => 'required|exists:proficiencies,id',
        ], [
            'subject_id.unique' => 'This subject is already in the pupil\'s diet.'
        ]));

        return back()->with('success', 'Diet Entry Added Successfully!');
    }

    public function update(Request $request, Diet $diet)
    {
        $diet->update($request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('diets')->where('pupil_id', $diet->pupil_id)->ignore($diet->id)
            ],
            'proficiency_id' => 'required|exists:proficiencies,id',
        ], [
            'subject_id.unique' => 'This subject is already in the pupil\'s diet.'
        ]));

        return back()->with('success', 'Diet Entry Updated Successfully!');
    }

    public function destroy(Diet $diet)
    {
        try {
            $diet->delete();
            return back()->with('success', 'Diet Entry Deleted Successfully!');
        } catch (QueryException $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
