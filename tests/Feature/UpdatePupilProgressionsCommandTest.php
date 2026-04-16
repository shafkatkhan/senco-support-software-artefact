<?php

namespace Tests\Feature;

use App\Models\Pupil;
use App\Models\PupilProgression;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePupilProgressionsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $group = \App\Models\UserGroup::factory()->create();
        \App\Models\User::factory()->create(['user_group_id' => $group->id]);
    }

    public function test_skips_when_settings_are_missing(): void
    {
        $this->artisan('app:update-pupil-progressions')
            ->expectsOutput('Progression settings not configured. Skipping.')
            ->assertExitCode(0);
    }

    public function test_skips_when_today_is_not_configured_date(): void
    {
        Setting::set('progression_update_date', date('m-d', strtotime('+1 day')));
        Setting::set('progression_min_year_group', '7');
        Setting::set('progression_max_year_group', '13');

        $today = date('m-d');

        $this->artisan('app:update-pupil-progressions')
            ->expectsOutput("Today ($today) is not the configured progression date (" . Setting::get('progression_update_date') . "). Skipping.")
            ->assertExitCode(0);
    }

    public function test_adds_next_year_group(): void
    {
        Setting::set('progression_update_date', date('m-d'));
        Setting::set('progression_min_year_group', '7');
        Setting::set('progression_max_year_group', '13');
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'auto_progression' => true,
        ]);
        PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2025/2026',
            'year_group' => 8,
            'type' => 'initial',
        ]);

        $this->artisan('app:update-pupil-progressions')
            ->expectsOutput('Found 1 pupils eligible for auto progression.')
            ->expectsOutput('Successfully progressed 1 pupils to their next year group.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('pupil_progressions', [
            'pupil_id' => $pupil->id,
            'academic_year' => date('Y') . '/' . (date('Y') + 1),
            'year_group' => 9,
            'type' => 'auto',
        ]);
    }

    public function test_skips_pupils_that_cannot_progress(): void
    {
        Setting::set('progression_update_date', date('m-d'));
        Setting::set('progression_min_year_group', '7');
        Setting::set('progression_max_year_group', '9');
        $noInitial = Pupil::factory()->create([
            'pupil_number' => 'PUP-002',
            'first_name' => 'No',
            'last_name' => 'Progression',
            'auto_progression' => true,
        ]);
        $atLimit = Pupil::factory()->create([
            'pupil_number' => 'PUP-003',
            'first_name' => 'Max',
            'last_name' => 'Limit',
            'auto_progression' => true,
        ]);
        PupilProgression::create([
            'pupil_id' => $atLimit->id,
            'academic_year' => '2025/2026',
            'year_group' => 9,
            'type' => 'initial',
        ]);
        $alreadyProgressed = Pupil::factory()->create([
            'pupil_number' => 'PUP-004',
            'first_name' => 'Already',
            'last_name' => 'Done',
            'auto_progression' => true,
        ]);
        PupilProgression::create([
            'pupil_id' => $alreadyProgressed->id,
            'academic_year' => '2025/2026',
            'year_group' => 7,
            'type' => 'initial',
        ]);
        PupilProgression::create([
            'pupil_id' => $alreadyProgressed->id,
            'academic_year' => date('Y') . '/' . (date('Y') + 1),
            'year_group' => 8,
            'type' => 'auto',
        ]);

        $this->artisan('app:update-pupil-progressions')
            ->expectsOutput('Found 3 pupils eligible for auto progression.')
            ->expectsOutput('No Progression (PUP-002) has no initial progression. Skipping.')
            ->expectsOutput('Max Limit (PUP-003) has reached the max year limit. Skipping.')
            ->expectsOutput('Already Done (PUP-004) already has a progression for the new academic year. Skipping.')
            ->expectsOutput('Successfully progressed 0 pupils to their next year group.')
            ->assertExitCode(0);
    }
}
