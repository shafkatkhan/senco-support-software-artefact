<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\Accommodation;
use App\Models\Diagnosis;
use App\Models\Medication;
use App\Models\Major;
use App\Models\Subject;
use App\Models\PupilProgression;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Show the reports page with filter options.
     */
    public function index()
    {
        // get distinct year groups from pupil progression settings
        $minYear = Setting::get('progression_min_year_group', 1);
        $maxYear = Setting::get('progression_max_year_group', 13);
        $year_groups = range($minYear, $maxYear);

        // get distinct diagnosis names (conditions)
        $conditions = Diagnosis::distinct()->orderBy('name')->pluck('name');

        // get all accommodations
        $accommodations = Accommodation::orderBy('name')->get();

        // get all majors
        $majors = Major::orderBy('name')->get();

        // get all subjects
        $subjects = Subject::orderBy('name')->get();

        // get distinct medication names
        $medications = Medication::distinct()->orderBy('name')->pluck('name');

        $title = __('Cohort Reports');
        return view('cohort_reports', compact('title', 'year_groups', 'conditions', 'accommodations', 'medications', 'majors', 'subjects'));
    }

    /**
     * Return JSON for the AJAX table.
     */
    public function preview(Request $request)
    {
        $pupils = $this->filteredPupils($request);

        $result = $pupils->map(function ($pupil) {
            return [
                'pupil_number' => $pupil->pupil_number,
                'first_name'   => $pupil->first_name,
                'last_name'    => $pupil->last_name,
                'gender'       => __('' . $pupil->gender),
                'major'        => $pupil->major ? $pupil->major->name : 'N/A',
                'year_group'   => $pupil->latestProgression?->year_group ?? 'N/A',
                'tutor_group'  => $pupil->latestProgression?->tutor_group ?? 'N/A',
                'diagnoses'    => $pupil->diagnoses->pluck('name')->implode('; <br><br>') ?: 'N/A',
                'medications'  => $pupil->medications->pluck('name')->implode('; <br><br>') ?: 'N/A',
                'subjects'     => $pupil->diets->map(fn ($d) => $d->subject?->name)->filter()->unique()->implode('; <br><br>') ?: 'N/A',
                'accommodations' => $pupil->diets->flatMap(function ($diet) {
                    $subjectName = $diet->subject?->name ?? __('N/A');
                    return $diet->accommodations->map(function ($acc) use ($subjectName) {
                        return __(':name (:status for :subject)', [
                            'name' => $acc->name,
                            'status' => __($acc->pivot->status),
                            'subject' => $subjectName,
                        ]);
                    });
                })->unique()->implode('; <br><br>') ?: __('N/A'),
            ];
        });

        return response()->json(['pupils' => $result->values()]);
    }

    /**
     * Export the filtered pupil data as a CSV or Excel spreadsheet.
     */
    public function export(Request $request)
    {
        $pupils = $this->filteredPupils($request);
        $format = $request->input('format', 'csv');
        $extension = strtolower($format) == 'csv' ? 'csv' : 'xlsx';

        $filename = 'pupils_report_' . now()->format('Y-m-d_His') . '.' . $extension;

        return Excel::download(new \App\Exports\ReportsExport($pupils), $filename);
    }

    /**
     * Shared filter logic used by both preview and export.
     */
    private function filteredPupils(Request $request)
    {
        $yearGroups = $request->input('year_group');
        $diagnoses = $request->input('diagnosis');
        $accommodationIds = $request->input('accommodation_ids');
        $medicationNames = $request->input('medication');
        $majorIds = $request->input('major_ids');
        $genders = $request->input('gender');
        $subjectIds = $request->input('subjects');

        $query = Pupil::with([
            'diagnoses',
            'medications',
            'latestProgression',
            'primaryFamilyMember',
            'socialServicesProfessional',
            'probationOfficerProfessional',
            'major',
            'diets.subject',
            'diets.accommodations',
        ]);

        // filter by year group (checks only the latest progression for each pupil)
        if (!empty($yearGroups)) {
            $query->whereHas('latestProgression', function ($q) use ($yearGroups) {
                $q->whereIn('year_group', $yearGroups);
            });
        }

        // filter by diagnosis name
        if ($diagnoses && is_array($diagnoses) && count($diagnoses) > 0) {
            $query->whereHas('diagnoses', function ($q) use ($diagnoses) {
                $q->whereIn('name', $diagnoses);
            });
        }

        // filter by accommodation (via diet pivot)
        if ($accommodationIds && is_array($accommodationIds) && count($accommodationIds) > 0) {
            $query->whereHas('diets.accommodations', function ($q) use ($accommodationIds) {
                $q->whereIn('accommodation_id', $accommodationIds);
            });
        }

        // filter by medication name
        if ($medicationNames && is_array($medicationNames) && count($medicationNames) > 0) {
            $query->whereHas('medications', function ($q) use ($medicationNames) {
                $q->whereIn('name', $medicationNames);
            });
        }

        // filter by gender
        if ($genders && is_array($genders) && count($genders) > 0) {
            $query->whereIn('gender', $genders);
        }

        // filter by major
        if (!empty($majorIds)) {
            $query->where(function ($q) use ($majorIds) {
                $q->whereIn('major_id', array_diff($majorIds, ['none']));
                if (in_array('none', $majorIds)) {
                    $q->orWhereNull('major_id');
                }
            });
        }

        // filter by subjects
        if ($subjectIds && is_array($subjectIds) && count($subjectIds) > 0) {
            $query->whereHas('diets', function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            });
        }

        return $query->get();
    }
}
