<?php

namespace Tests\Feature;

use App\Exports\ReportsExport;
use App\Models\Accommodation;
use App\Models\Diagnosis;
use App\Models\Diet;
use App\Models\Major;
use App\Models\Medication;
use App\Models\Pupil;
use App\Models\PupilProgression;
use App\Models\Setting;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('reports.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['export-cohort-reports']);
        Setting::set('progression_min_year_group', 7);
        Setting::set('progression_max_year_group', 9);
        Diagnosis::factory()->create(['name' => 'Dyslexia']);
        Accommodation::factory()->create(['name' => 'Reader']);
        Medication::factory()->create(['name' => 'Salbutamol']);
        Major::factory()->create(['name' => 'Creative Arts']);
        Subject::factory()->create(['name' => 'Mathematics']);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('cohort_reports');
        $response->assertViewHas('title');
        $response->assertViewHas('year_groups', [7, 8, 9]);
        $response->assertViewHas('conditions');
        $response->assertViewHas('accommodations');
        $response->assertViewHas('medications');
        $response->assertViewHas('majors');
        $response->assertViewHas('subjects');
    }

    public function test_preview_requires_authentication(): void
    {
        $response = $this->get(route('reports.preview'));
        $response->assertRedirect(route('login'));
    }

    public function test_preview_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('reports.preview'));
        $response->assertForbidden();
    }

    public function test_preview_returns_filtered_pupil_data(): void
    {
        $user = $this->userWithPermissions(['export-cohort-reports']);
        $major = Major::factory()->create(['name' => 'Science']);
        $subject = Subject::factory()->create(['name' => 'Biology']);
        $accommodation = Accommodation::factory()->create(['name' => 'Extra Time']);
        $matchingPupil = $this->createPupil([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'major_id' => $major->id,
        ]);
        $otherPupil = $this->createPupil([
            'gender' => 'Male',
        ]);

        PupilProgression::create([
            'pupil_id' => $matchingPupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);
        PupilProgression::create([
            'pupil_id' => $otherPupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 9,
            'tutor_group' => '9B',
            'type' => 'manual',
        ]);
        Diagnosis::factory()->create([
            'pupil_id' => $matchingPupil->id,
            'name' => 'Dyslexia',
        ]);
        Medication::factory()->create([
            'pupil_id' => $matchingPupil->id,
            'name' => 'Salbutamol',
        ]);
        $diet = Diet::factory()->create([
            'pupil_id' => $matchingPupil->id,
            'subject_id' => $subject->id,
        ]);
        $diet->accommodations()->attach($accommodation->id, [
            'status' => 'Approved',
            'details' => 'Use during assessments.',
        ]);

        $response = $this->actingAs($user)->get(route('reports.preview', [
            'year_group' => [8],
            'diagnosis' => ['Dyslexia'],
            'accommodation_ids' => [$accommodation->id],
            'medication' => ['Salbutamol'],
            'gender' => ['Female'],
            'major_ids' => [$major->id],
            'subjects' => [$subject->id],
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'pupils');
        $response->assertJsonPath('pupils.0.pupil_number', 'PUP-001');
        $response->assertJsonPath('pupils.0.first_name', 'Jane');
        $response->assertJsonPath('pupils.0.major', 'Science');
        $response->assertJsonPath('pupils.0.year_group', 8);
        $response->assertJsonPath('pupils.0.tutor_group', '8A');
        $this->assertStringContainsString('Dyslexia', $response->json('pupils.0.diagnoses'));
        $this->assertStringContainsString('Salbutamol', $response->json('pupils.0.medications'));
        $this->assertStringContainsString('Biology', $response->json('pupils.0.subjects'));
        $this->assertStringContainsString('Extra Time', $response->json('pupils.0.accommodations'));
    }

    public function test_preview_filters_pupils_without_major(): void
    {
        $user = $this->userWithPermissions(['export-cohort-reports']);
        $pupilWithoutMajor = $this->createPupil([
            'pupil_number' => 'PUP-002',
            'major_id' => null,
        ]);
        $this->createPupil([
            'major_id' => Major::factory()->create()->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.preview', [
            'major_ids' => ['none'],
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'pupils');
        $response->assertJsonPath('pupils.0.pupil_number', $pupilWithoutMajor->pupil_number);
        $response->assertJsonPath('pupils.0.major', 'N/A');
    }

    public function test_export_requires_authentication(): void
    {
        $response = $this->get(route('reports.export'));
        $response->assertRedirect(route('login'));
    }

    public function test_export_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('reports.export'));
        $response->assertForbidden();
    }

    public function test_export_downloads_filtered_report(): void
    {
        Excel::fake();
        Carbon::setTestNow(Carbon::parse('2026-04-16 15:30:45'));
        $user = $this->userWithPermissions(['export-cohort-reports']);
        $pupil = $this->createPupil(['gender' => 'Female']);
        $this->createPupil(['gender' => 'Male']);

        $response = $this->actingAs($user)->get(route('reports.export', [
            'gender' => ['Female'],
            'format' => 'xlsx',
        ]));

        $response->assertOk();
        Excel::assertDownloaded('pupils_report_2026-04-16_153045.xlsx', function ($export) use ($pupil) {
            return $export instanceof ReportsExport
                && $export->collection()->pluck('id')->all() === [$pupil->id];
        });
        Carbon::setTestNow();
    }

    public function test_report_export_maps_related_pupil_data(): void
    {
        $major = Major::factory()->create(['name' => 'Science']);
        $subject = Subject::factory()->create(['name' => 'Biology']);
        $accommodation = Accommodation::factory()->create(['name' => 'Extra Time']);
        $pupil = $this->createPupil([
            'pupil_number' => 'PUP-003',
            'first_name' => 'Alex',
            'last_name' => 'Smith',
            'gender' => 'Other',
            'major_id' => $major->id,
            'dob' => '2010-04-16',
            'joined_date' => '2024-09-01',
            'auto_progression' => true,
            'smoking_history' => false,
            'drug_abuse_history' => false,
        ]);
        PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);
        Diagnosis::factory()->create([
            'pupil_id' => $pupil->id,
            'name' => 'Dyslexia',
        ]);
        Medication::factory()->create([
            'pupil_id' => $pupil->id,
            'name' => 'Salbutamol',
        ]);
        $diet = Diet::factory()->create([
            'pupil_id' => $pupil->id,
            'subject_id' => $subject->id,
        ]);
        $diet->accommodations()->attach($accommodation->id, ['status' => 'Approved']);

        $export = new ReportsExport(collect([$pupil->fresh()->load([
            'major',
            'latestProgression',
            'diagnoses',
            'medications',
            'diets.subject',
            'diets.accommodations',
        ])]));
        $row = $export->map($export->collection()->first());

        $this->assertEquals('PUP-003', $row[0]);
        $this->assertEquals('Alex', $row[1]);
        $this->assertEquals('Science', $row[3]);
        $this->assertEquals('16/04/2010', $row[4]);
        $this->assertEquals(8, $row[32]);
        $this->assertEquals('8A', $row[33]);
        $this->assertStringContainsString('Dyslexia', $row[34]);
        $this->assertStringContainsString('Salbutamol', $row[35]);
        $this->assertStringContainsString('Biology', $row[36]);
        $this->assertStringContainsString('Extra Time', $row[37]);
    }

    protected function createPupil(array $attributes = []): Pupil
    {
        $onboarder = $this->userWithPermissions([]);

        return Pupil::factory()->create(array_merge([
            'onboarded_by' => $onboarder->id,
        ], $attributes));
    }
}
