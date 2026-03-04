<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\Accommodation;
use App\Models\RecordType;
use App\Models\Professional;
use App\Models\MeetingType;

class PupilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pupil $pupil)
    {
        $pupil->load('medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Details";
        return view('pupils.show', compact('pupil', 'title'));
    }

    public function medications(Pupil $pupil)
    {
        $pupil->load('medications');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Medications";
        return view('pupils.medications', compact('pupil', 'title'));
    }

    public function diagnoses(Pupil $pupil)
    {
        $pupil->load('diagnoses.professional');
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Diagnoses";
        return view('pupils.diagnoses', compact('pupil', 'title', 'professionals'));
    }

    public function records(Pupil $pupil)
    {
        $pupil->load(['records.recordType', 'records.professional']);
        $record_types = RecordType::all();
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Records";
        return view('pupils.records', compact('pupil', 'title', 'record_types', 'professionals'));
    }

    public function events(Pupil $pupil)
    {
        $pupil->load('events');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Events";
        return view('pupils.events', compact('pupil', 'title'));
    }

    public function accommodations(Pupil $pupil)
    {
        $pupil->load('accommodations');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Accommodations";
        
        // get accommodations the pupil doesn't already have
        $existingAccommodationIds = $pupil->accommodations->pluck('id')->toArray();
        $availableAccommodations = Accommodation::whereNotIn('id', $existingAccommodationIds)->get();

        return view('pupils.accommodations', compact('pupil', 'title', 'availableAccommodations'));
    }

    public function familyMembers(Pupil $pupil)
    {
        $pupil->load('familyMembers');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Family Members";
        return view('pupils.family_members', compact('pupil', 'title'));
    }

    public function meetings(Pupil $pupil)
    {
        $pupil->load(['meetings.meetingType']);
        $meeting_types = MeetingType::all();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Meetings";
        return view('pupils.meetings', compact('pupil', 'title', 'meeting_types'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
