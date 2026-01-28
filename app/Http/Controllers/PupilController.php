<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;

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
    public function show(string $id)
    {
        $pupil = Pupil::with('medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses')->findOrFail($id);
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Details";
        return view('pupils.show', compact('pupil', 'title'));
    }

    public function medications(string $id)
    {
        $pupil = Pupil::with('medications')->findOrFail($id);
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Medications";
        return view('pupils.medications', compact('pupil', 'title'));
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
