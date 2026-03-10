<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\RecordType;
use App\Models\Professional;
use App\Models\MeetingType;
use App\Models\Subject;
use App\Models\Proficiency;
use Illuminate\Support\Facades\Gate;

class PupilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-pupils');
        
        $pupils = Pupil::with('medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses')->get();
        $title = "Pupils";
        return view('pupils', compact('pupils', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-pupils');
        
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pupil $pupil)
    {
        Gate::authorize('view-pupils');

        $pupil->load('medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses.professional', 'records.professional', 'records.recordType');
        
        // build a grouped list of professional involvements
        $grouped = [];
        foreach ($pupil->diagnoses as $diagnosis) {
            if ($diagnosis->professional) {
                $id = $diagnosis->professional->id;
                $grouped[$id]['professional'] = $diagnosis->professional;
                $grouped[$id]['involvements'][] = $diagnosis->name . ' Diagnosis';
            }
        }
        foreach ($pupil->records as $record) {
            if ($record->professional) {
                $id = $record->professional->id;
                $grouped[$id]['professional'] = $record->professional;
                $grouped[$id]['involvements'][] = $record->recordType->name . ' Record';
            }
        }
        $involvements = collect(array_values($grouped));

        $title = $pupil->first_name . " " . $pupil->last_name . "'s Details";
        return view('pupils.show', compact('pupil', 'title', 'involvements'));
    }

    public function medications(Pupil $pupil)
    {
        Gate::authorize('view-medications');

        $pupil->load('medications');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Medications";
        return view('pupils.medications', compact('pupil', 'title'));
    }

    public function diagnoses(Pupil $pupil)
    {
        Gate::authorize('view-diagnoses');

        $pupil->load('diagnoses.professional');
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Diagnoses";
        return view('pupils.diagnoses', compact('pupil', 'title', 'professionals'));
    }

    public function records(Pupil $pupil)
    {
        Gate::authorize('view-records');

        $pupil->load(['records.recordType', 'records.professional']);
        $record_types = RecordType::all();
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Records";
        return view('pupils.records', compact('pupil', 'title', 'record_types', 'professionals'));
    }

    public function events(Pupil $pupil)
    {
        Gate::authorize('view-events');

        $pupil->load('events');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Events";
        return view('pupils.events', compact('pupil', 'title'));
    }

    public function familyMembers(Pupil $pupil)
    {
        Gate::authorize('view-family-members');

        $pupil->load('familyMembers');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Family Members";
        return view('pupils.family_members', compact('pupil', 'title'));
    }

    public function meetings(Pupil $pupil)
    {
        Gate::authorize('view-meetings');

        $pupil->load(['meetings.meetingType']);
        $meeting_types = MeetingType::all();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Meetings";
        return view('pupils.meetings', compact('pupil', 'title', 'meeting_types'));
    }

    public function diets(Pupil $pupil)
    {
        Gate::authorize('view-diets');

        $pupil->load(['diets.subject', 'diets.proficiency', 'diets.accommodations']);
        $subjects = Subject::with(['proficiencies', 'accommodations'])->orderBy('name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Diet";
        return view('pupils.diets', compact('pupil', 'title', 'subjects'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('edit-pupils');

        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete-pupils');
        
        //
    }
}
