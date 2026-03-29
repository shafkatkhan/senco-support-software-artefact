<?php

namespace App\Imports;

use App\Models\Pupil;
use App\Models\PupilProgression;
use App\Models\Setting;
use App\Models\Major;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PupilsImport implements ToCollection, WithHeadingRow
{
    private $importedCount = 0;

    /**
     * Import pupils from a collection.
     */
    public function collection(Collection $rows)
    {
        $progression_configured = Setting::get('progression_update_date') && Setting::exists('progression_min_year_group') && Setting::exists('progression_max_year_group');
        $currentYearInt = (int)date('Y');
        $academicYear = $currentYearInt . '/' . ($currentYearInt + 1);

        DB::transaction(function () use ($rows, $progression_configured, $academicYear) {
            foreach ($rows as $row) {
                // ensure required fields exist
                if (empty($row['pupil_number']) || empty($row['first_name']) || empty($row['last_name']) || empty($row['year_group'])) {
                    continue; // skip invalid rows without throwing errors
                }

                $dob = $this->parseDate($row['dob'] ?? null);
                $joinedDate = $this->parseDate($row['joined_date'] ?? null);

                $majorId = null;
                if (!empty($row['major'])) {
                    $major = Major::where('name', 'like', trim($row['major']))->first();
                    if ($major) {
                        $majorId = $major->id;
                    }
                }

                $pupilData = [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'dob' => $dob,
                    'gender' => ucfirst(strtolower($row['gender'] ?? 'Other')),
                    'address_line_1' => $row['address_line_1'] ?? null,
                    'address_line_2' => $row['address_line_2'] ?? null,
                    'locality' => $row['locality'] ?? null,
                    'postcode' => $row['postcode'] ?? null,
                    'country' => $row['country'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'after_school_job' => $row['after_school_job'] ?? null,
                    'joined_date' => $joinedDate,
                    'auto_progression' => $progression_configured,
                    'major_id' => $majorId,
                    'parental_description' => $row['parental_description'] ?? null,
                    'has_special_needs' => filter_var($row['has_special_needs'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'special_needs_details' => $row['special_needs_details'] ?? null,
                    'attended_special_school' => filter_var($row['attended_special_school'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'special_school_details' => $row['special_school_details'] ?? null,
                    'smoking_history' => filter_var($row['smoking_history'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'drug_abuse_history' => filter_var($row['drug_abuse_history'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'treatment_plan' => $row['treatment_plan'] ?? null,
                ];

                // update or create by pupil number
                $pupil = Pupil::updateOrCreate(
                    ['pupil_number' => $row['pupil_number']],
                    array_merge($pupilData, ['onboarded_by' => auth()->id()])
                );

                $this->importedCount++;

                // initial progression
                PupilProgression::updateOrCreate([
                    'pupil_id' => $pupil->id,
                    'academic_year' => $academicYear,
                    'type' => 'initial'
                ], [
                    'year_group' => $row['year_group'],
                    'tutor_group' => $row['tutor_group'] ?? null,
                ]);
            }
        });
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Parse a date string or Excel date into a Y-m-d format.
     */
    private function parseDate($dateStr)
    {
        if (empty($dateStr)) return null;
        try {
            if (is_numeric($dateStr)) {
                return Date::excelToDateTimeObject($dateStr)->format('Y-m-d');
            }

            // convert slashes to dashes to enforce correct date format before parsing
            $dateStr = str_replace('/', '-', $dateStr);
            
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
