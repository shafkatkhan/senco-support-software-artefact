<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\Accommodation;

class PupilAccommodationController extends Controller
{
    public function store(Request $request, Pupil $pupil)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
        ]);

        $pupil->accommodations()->syncWithoutDetaching([$request->accommodation_id]);

        return redirect()->back()->with('success', 'Accommodation added successfully.');
    }

    public function destroy(Pupil $pupil, Accommodation $accommodation)
    {
        $pupil->accommodations()->detach($accommodation->id);

        return redirect()->back()->with('success', 'Accommodation removed successfully.');
    }
}
