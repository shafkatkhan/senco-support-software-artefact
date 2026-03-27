<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DietController extends Controller
{
    use \App\Traits\ExportsPupilData;

    public function store(Request $request)
    {
        Gate::authorize('add-to-diets');

        $hasProficiencies = DB::table('subject_proficiencies')->where('subject_id', $request->subject_id)->exists();

        $validatedData = $request->validate([
            'pupil_id' => 'required|exists:pupils,id',
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('diets')->where('pupil_id', $request->pupil_id)
            ],
            'proficiency_id' => [
                Rule::requiredIf($hasProficiencies),
                'nullable',
                Rule::exists('subject_proficiencies', 'proficiency_id')->where('subject_id', $request->subject_id)
            ],
            'accommodations' => 'nullable|array',
            'accommodations.*.id' => [
                'required',
                'exists:accommodations,id',
                Rule::exists('subject_accommodations', 'accommodation_id')->where('subject_id', $request->subject_id)
            ],
            'accommodations.*.status' => 'required|in:Recommended,Approved',
            'accommodations.*.details' => 'nullable|string'
        ], [
            'subject_id.unique' => __('This subject is already in the pupil\'s diet.')
        ]);

        $diet = Diet::create([
            'pupil_id' => $validatedData['pupil_id'],
            'subject_id' => $validatedData['subject_id'],
            'proficiency_id' => $validatedData['proficiency_id'] ?? null,
        ]);

        $syncData = collect($validatedData['accommodations'] ?? [])
            ->unique('id')
            ->mapWithKeys(fn($acc) => [
                $acc['id'] => [
                    'status' => $acc['status'],
                    'details' => $acc['details'] ?? null
                ]
            ]);

        $diet->accommodations()->sync($syncData);

        return back()->with('success', __('Successfully added ":name" to diet!', ['name' => $diet->subject->name]));
    }

    public function update(Request $request, Diet $diet)
    {
        Gate::authorize('edit-diets');

        $hasProficiencies = DB::table('subject_proficiencies')->where('subject_id', $request->subject_id)->exists();

        $validatedData = $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('diets')->where('pupil_id', $diet->pupil_id)->ignore($diet->id)
            ],
            'proficiency_id' => [
                Rule::requiredIf($hasProficiencies),
                'nullable',
                Rule::exists('subject_proficiencies', 'proficiency_id')->where('subject_id', $request->subject_id)
            ],
            'accommodations' => 'nullable|array',
            'accommodations.*.id' => [
                'required',
                'exists:accommodations,id',
                Rule::exists('subject_accommodations', 'accommodation_id')->where('subject_id', $request->subject_id)
            ],
            'accommodations.*.status' => 'required|in:Recommended,Approved',
            'accommodations.*.details' => 'nullable|string'
        ], [
            'subject_id.unique' => __('This subject is already in the pupil\'s diet.')
        ]);

        $diet->update([
            'subject_id' => $validatedData['subject_id'],
            'proficiency_id' => $validatedData['proficiency_id'] ?? null,
        ]);

        $syncData = collect($validatedData['accommodations'] ?? [])
            ->unique('id')
            ->mapWithKeys(fn($acc) => [
                $acc['id'] => [
                    'status' => $acc['status'],
                    'details' => $acc['details'] ?? null
                ]
            ]);

        $diet->accommodations()->sync($syncData);

        return back()->with('success', __('Successfully updated diet entry for ":name"!', ['name' => $diet->subject->name]));
    }

    public function destroy(Diet $diet)
    {
        Gate::authorize('delete-diets');
        
        try {
            $diet->delete();
            return back()->with('success', __('Successfully removed ":name" from diet!', ['name' => $diet->subject->name]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
