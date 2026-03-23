<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Setting;
use App\Models\Pupil;
use App\Models\PupilProgression;

class UpdatePupilProgressions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-pupil-progressions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically progress pupils to the next year group on the specified annual date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updateDate = Setting::get('progression_update_date');
        $minYearLimit = Setting::get('progression_min_year_group');
        $maxYearLimit = Setting::get('progression_max_year_group');

        if (!$updateDate || !$minYearLimit || !$maxYearLimit) {
            $this->info("Progression settings not configured. Skipping.");
            return;
        }

        $today = date('m-d');

        if ($today != $updateDate) {
            $this->info("Today ($today) is not the configured progression date ($updateDate). Skipping.");
            return;
        }

        $pupils = Pupil::where('auto_progression', true)->get();

        $this->info("Found " . $pupils->count() . " pupils eligible for auto progression.");

        $addedCount = 0;

        foreach ($pupils as $pupil) {
            $latestProgression = $pupil->progressions()->latest()->first();

            // cannot auto-progress a pupil without any initial progression to increment
            if (!$latestProgression) {
                $this->info($pupil->first_name . " " . $pupil->last_name . " (" . $pupil->pupil_number . ") has no initial progression. Skipping.");
                continue;
            }

            $nextYearGroup = $latestProgression->year_group + 1;

            // pupil has reached the max year limit, stop progressing
            if ($nextYearGroup > $maxYearLimit) {
                $this->info($pupil->first_name . " " . $pupil->last_name . " (" . $pupil->pupil_number . ") has reached the max year limit. Skipping.");
                continue;
            }

            // determine new academic year based on current date, eg. "2025/2026"
            $currentYearInt = (int)date('Y');
            $nextAcademicYear = $currentYearInt . '/' . ($currentYearInt + 1);

            // in case the pupil already has a progression for the new academic year, skip
            $exists = $pupil->progressions()->where('academic_year', $nextAcademicYear)->exists();
            if ($exists) {
                $this->info($pupil->first_name . " " . $pupil->last_name . " (" . $pupil->pupil_number . ") already has a progression for the new academic year. Skipping.");
                continue;
            }

            PupilProgression::create([
                'pupil_id' => $pupil->id,
                'academic_year' => $nextAcademicYear,
                'year_group' => $nextYearGroup,
                'type' => 'auto'
            ]);
            $addedCount++;
        }

        $this->info("Successfully progressed $addedCount pupils to their next year group.");
    }
}
