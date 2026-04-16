<?php

namespace Tests\Feature;

use App\Imports\PupilsImport;
use App\Models\Major;
use App\Models\Pupil;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PupilsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_collection_imports_valid_rows_and_skips_invalid_rows(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create(['user_group_id' => $group->id]);
        $major = Major::factory()->create(['name' => 'Car Electronics']);
        Setting::set('progression_update_date', '2026-09-01');
        Setting::set('progression_min_year_group', '7');
        Setting::set('progression_max_year_group', '13');

        $import = new PupilsImport();

        $this->actingAs($user);
        $import->collection(new Collection([
            [
                'pupil_number' => 'PUP-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'dob' => '14/05/2008',
                'gender' => 'male',
                'year_group' => '10',
                'tutor_group' => '10A',
                'major' => 'Car Electronics',
                'phone' => '07123456789',
                'email' => 'john@example.com',
                'has_special_needs' => 'yes',
                'attended_special_school' => '0',
                'smoking_history' => 'false',
                'drug_abuse_history' => '1',
                'joined_date' => 45000,
                'treatment_plan' => 'Support plan',
            ],
            [
                'pupil_number' => '',
                'first_name' => 'Skipped',
                'last_name' => 'Pupil',
                'year_group' => '8',
            ],
        ]));

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertDatabaseHas('pupils', [
            'pupil_number' => 'PUP-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Male',
            'major_id' => $major->id,
            'auto_progression' => true,
            'onboarded_by' => $user->id,
            'has_special_needs' => true,
            'drug_abuse_history' => true,
            'treatment_plan' => 'Support plan',
        ]);

        $pupil = Pupil::where('pupil_number', 'PUP-001')->first();
        $this->assertEquals('2008-05-14', $pupil->dob->format('Y-m-d'));
        $this->assertDatabaseHas('pupil_progressions', [
            'pupil_id' => $pupil->id,
            'year_group' => '10',
            'tutor_group' => '10A',
            'type' => 'initial',
        ]);
    }

    public function test_collection_updates_existing_pupil_and_handles_invalid_dates_and_missing_major(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create(['user_group_id' => $group->id]);
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-002',
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $import = new PupilsImport();

        $this->actingAs($user);
        $import->collection(new Collection([
            [
                'pupil_number' => 'PUP-002',
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'dob' => '2008-05-14',
                'joined_date' => 'not-a-date',
                'gender' => null,
                'year_group' => '11',
                'tutor_group' => null,
                'major' => 'Missing Major',
            ],
        ]));

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertEquals($pupil->id, Pupil::where('pupil_number', 'PUP-002')->first()->id);
        $this->assertDatabaseHas('pupils', [
            'id' => $pupil->id,
            'first_name' => 'Updated',
            'gender' => 'Other',
            'joined_date' => null,
            'major_id' => null,
            'auto_progression' => false,
        ]);
    }
}
