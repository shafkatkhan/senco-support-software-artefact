<?php

namespace Tests\Feature;

use App\Models\Diagnosis;
use App\Models\Diet;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Major;
use App\Models\Medication;
use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\Professional;
use App\Models\Proficiency;
use App\Models\Pupil;
use App\Models\PupilProgression;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\SchoolHistory;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TreatmentPlanUpdate;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PupilTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('pupils.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('pupils.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('pupils');
        $this->createPupil();

        $response = $this->actingAs($user)->get(route('pupils.index'));

        $response->assertOk();
        $response->assertViewIs('pupils');
        $response->assertViewHas('pupils');
        $response->assertViewHas('title', 'SEND Pupils');
    }

    public function test_create_requires_authentication(): void
    {
        $response = $this->get(route('pupils.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_create_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('pupils');
        $response = $this->actingAs($user)->get(route('pupils.create'));
        $response->assertForbidden();
    }

    public function test_create_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['create-pupils']);
        Professional::factory()->create(['last_name' => 'Worker']);
        RecordType::factory()->create(['name' => 'Assessment']);
        Major::factory()->create(['name' => 'Science']);

        $response = $this->actingAs($user)->get(route('pupils.create'));

        $response->assertOk();
        $response->assertViewIs('pupils.create');
        $response->assertViewHas('professionals');
        $response->assertViewHas('record_types');
        $response->assertViewHas('majors');
        $response->assertViewHas('title', 'Onboard New Pupil');
    }

    public function test_show_import_form_returns_view(): void
    {
        $user = $this->userWithPermissions(['bulk-import-pupils']);

        $response = $this->actingAs($user)->get(route('pupils.import.form'));

        $response->assertOk();
        $response->assertViewIs('pupils.import');
        $response->assertViewHas('title', 'Import Pupils');
    }

    public function test_import_validates_file(): void
    {
        $user = $this->userWithPermissions(['bulk-import-pupils']);

        $response = $this->actingAs($user)->post(route('pupils.import'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_processes_uploaded_file(): void
    {
        Excel::fake();
        $user = $this->userWithPermissions(['bulk-import-pupils']);

        $response = $this->actingAs($user)->post(route('pupils.import'), [
            'file' => UploadedFile::fake()->createWithContent('pupils.csv', "pupil_number,first_name,last_name\n"),
        ]);

        $response->assertRedirect(route('pupils.index'));
        $response->assertSessionHas('success');
    }

    public function test_import_redirects_back_when_import_fails(): void
    {
        Excel::shouldReceive('import')->andThrow(new \Exception('Bad file'));
        $user = $this->userWithPermissions(['bulk-import-pupils']);

        $response = $this->actingAs($user)->post(route('pupils.import'), [
            'file' => UploadedFile::fake()->createWithContent('pupils.csv', "pupil_number,first_name,last_name\n"),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_download_template_returns_csv(): void
    {
        $user = $this->userWithPermissions(['bulk-import-pupils']);

        $response = $this->actingAs($user)->get(route('pupils.import.template'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('pupil_number', $response->streamedContent());
        $this->assertStringContainsString('treatment_plan', $response->streamedContent());
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->post(route('pupils.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('pupils.store'), $this->pupilPayload());
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('pupils');
        $response = $this->actingAs($user)->post(route('pupils.store'), $this->pupilPayload());
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->userWithPermissions(['create-pupils']);

        $response = $this->actingAs($user)->post(route('pupils.store'), [
            'pupil_number' => '',
            'first_name' => '',
            'last_name' => '',
            'dob' => '',
            'gender' => '',
            'year_group' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_number', 'first_name', 'last_name', 'dob', 'gender', 'year_group']);
    }

    public function test_store_creates_pupil_and_related_records(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-16 12:00:00'));
        Setting::set('progression_update_date', '09-01');
        Setting::set('progression_min_year_group', 7);
        Setting::set('progression_max_year_group', 11);
        $user = $this->userWithPermissions(['create-pupils']);
        $major = Major::factory()->create();
        $recordType = RecordType::factory()->create();
        $existingProfessional = Professional::factory()->create();

        $response = $this->actingAs($user)->post(route('pupils.store'), $this->pupilPayload([
            'major_id' => $major->id,
            'social_services_involvement' => '1',
            'social_worker' => [
                'professional_id' => $existingProfessional->id,
            ],
            'probation_officer_required' => '1',
            'probation_officer' => [
                'is_new_professional' => '1',
                'prof_first_name' => 'Paula',
                'prof_last_name' => 'Officer',
            ],
            'family_members' => [
                [
                    'first_name' => 'Parent',
                    'last_name' => 'One',
                    'relation' => 'Parent',
                ],
            ],
            'school_histories' => [
                [
                    'school_name' => 'Previous School',
                    'years_attended' => 2,
                ],
            ],
            'diagnoses' => [
                [
                    'name' => 'Dyslexia',
                    'date' => '2026-01-01',
                    'is_new_professional' => '1',
                    'prof_first_name' => 'Dina',
                    'prof_last_name' => 'Doctor',
                ],
            ],
            'medications' => [
                [
                    'name' => 'Salbutamol',
                    'frequency' => 'Daily',
                    'start_date' => '2026-01-01',
                    'self_administer' => '1',
                ],
            ],
            'records' => [
                [
                    'record_type_id' => $recordType->id,
                    'title' => 'Initial Assessment',
                    'description' => 'Initial assessment notes.',
                    'is_new_professional' => '1',
                    'prof_first_name' => 'James',
                    'prof_last_name' => 'Smith',
                ],
            ],
        ]));

        $response->assertSessionHasNoErrors();
        $pupil = Pupil::where('pupil_number', 'PUP-001')->first();

        $response->assertRedirect(route('pupils.show', $pupil));
        $response->assertSessionHas('success');
        $this->assertTrue($pupil->auto_progression);
        $this->assertEquals($user->id, $pupil->onboarded_by);
        $this->assertEquals($existingProfessional->id, $pupil->social_services_professional_id);
        $this->assertNotNull($pupil->probation_officer_professional_id);
        $this->assertEquals('Parent', $pupil->primaryFamilyMember->first_name);
        $this->assertDatabaseHas('pupil_progressions', [
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'initial',
        ]);
        $this->assertDatabaseHas('school_histories', ['pupil_id' => $pupil->id, 'school_name' => 'Previous School']);
        $this->assertDatabaseHas('diagnoses', ['pupil_id' => $pupil->id, 'name' => 'Dyslexia']);
        $this->assertDatabaseHas('medications', ['pupil_id' => $pupil->id, 'name' => 'Salbutamol']);
        $this->assertDatabaseHas('records', ['pupil_id' => $pupil->id, 'title' => 'Initial Assessment']);
        $this->assertDatabaseHas('events', ['pupil_id' => $pupil->id, 'title' => 'Pupil Onboarded']);

        Carbon::setTestNow();
    }

    public function test_store_redirects_back_when_onboarding_fails(): void
    {
        $user = $this->userWithPermissions(['create-pupils']);
        Event::creating(function () {
            throw new \Exception('Event failed');
        });

        $response = $this->actingAs($user)->post(route('pupils.store'), $this->pupilPayload());

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseMissing('pupils', ['pupil_number' => 'PUP-001']);

        Event::flushEventListeners();
    }

    public function test_show_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('pupils');
        $pupil = $this->createPupil([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        TreatmentPlanUpdate::create([
            'pupil_id' => $pupil->id,
            'user_id' => $user->id,
            'date' => '2026-04-16',
            'description' => 'Reviewed plan.',
        ]);

        $response = $this->actingAs($user)->get(route('pupils.show', $pupil));

        $response->assertOk();
        $response->assertViewIs('pupils.show');
        $response->assertViewHas('pupil');
        $response->assertViewHas('involvements');
        $response->assertViewHas('title', 'Dashboard for Jane Doe');
    }

    public function test_detail_pages_return_expected_views(): void
    {
        $user = $this->userWithPermissions([
            'view-pupils',
            'view-medications',
            'view-diagnoses',
            'view-records',
            'view-events',
            'view-family-members',
            'view-meetings',
            'view-diets',
            'view-school-histories',
            'manage-attachments',
        ]);
        Setting::set('progression_update_date', '09-01');
        Setting::set('progression_min_year_group', 7);
        Setting::set('progression_max_year_group', 11);
        $pupil = $this->createPupil(['first_name' => 'Jane', 'last_name' => 'Doe']);
        Medication::factory()->create(['pupil_id' => $pupil->id]);
        Diagnosis::factory()->create(['pupil_id' => $pupil->id, 'professional_id' => Professional::factory()->create()->id]);
        Record::factory()->create(['pupil_id' => $pupil->id]);
        Event::factory()->create(['pupil_id' => $pupil->id]);
        FamilyMember::factory()->create(['pupil_id' => $pupil->id]);
        Meeting::factory()->create(['pupil_id' => $pupil->id]);
        Diet::factory()->create(['pupil_id' => $pupil->id]);
        SchoolHistory::factory()->create(['pupil_id' => $pupil->id]);
        PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'initial',
        ]);

        $this->actingAs($user)->get(route('pupils.progressions', $pupil))->assertOk()->assertViewIs('pupils.progressions');
        $this->actingAs($user)->get(route('pupils.medications', $pupil))->assertOk()->assertViewIs('pupils.medications');
        $this->actingAs($user)->get(route('pupils.diagnoses', $pupil))->assertOk()->assertViewIs('pupils.diagnoses');
        $this->actingAs($user)->get(route('pupils.records', $pupil))->assertOk()->assertViewIs('pupils.records');
        $this->actingAs($user)->get(route('pupils.events', $pupil))->assertOk()->assertViewIs('pupils.events');
        $this->actingAs($user)->get(route('pupils.family_members', $pupil))->assertOk()->assertViewIs('pupils.family_members');
        $this->actingAs($user)->get(route('pupils.meetings', $pupil))->assertOk()->assertViewIs('pupils.meetings');
        $this->actingAs($user)->get(route('pupils.diets', $pupil))->assertOk()->assertViewIs('pupils.diets');
        $this->actingAs($user)->get(route('pupils.school_histories', $pupil))->assertOk()->assertViewIs('pupils.school_histories');
        $this->actingAs($user)->get(route('pupils.attachments', $pupil))->assertOk()->assertViewIs('pupils.attachments');
    }

    public function test_edit_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['edit-pupils']);
        $pupil = $this->createPupil(['first_name' => 'Jane', 'last_name' => 'Doe']);

        $response = $this->actingAs($user)->get(route('pupils.edit', $pupil));

        $response->assertOk();
        $response->assertViewIs('pupils.edit');
        $response->assertViewHas('pupil');
        $response->assertViewHas('professionals');
        $response->assertViewHas('majors');
        $response->assertViewHas('title', 'Edit information for Jane Doe');
    }

    public function test_update_requires_authentication(): void
    {
        $pupil = $this->createPupil();
        $response = $this->put(route('pupils.update', $pupil), $this->pupilPayload());
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('pupils');
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->put(route('pupils.update', $pupil), $this->pupilPayload());
        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = $this->userWithPermissions(['edit-pupils']);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->put(route('pupils.update', $pupil), [
            'pupil_number' => '',
            'first_name' => '',
            'last_name' => '',
            'dob' => '',
            'gender' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_number', 'first_name', 'last_name', 'dob', 'gender']);
    }

    public function test_update_modifies_pupil_and_clears_disabled_details(): void
    {
        $user = $this->userWithPermissions(['edit-pupils']);
        $major = Major::factory()->create();
        $familyMember = FamilyMember::factory()->create(['pupil_id' => $this->createPupil()->id]);
        $pupil = $familyMember->pupil;

        $response = $this->actingAs($user)->put(route('pupils.update', $pupil), $this->pupilPayload([
            'pupil_number' => $pupil->pupil_number,
            'first_name' => 'Updated',
            'last_name' => 'Pupil',
            'email' => 'updated@example.com',
            'major_id' => $major->id,
            'primary_family_member_id' => $familyMember->id,
            'has_special_needs' => false,
            'special_needs_details' => 'Should clear',
            'attended_special_school' => false,
            'special_school_details' => 'Should clear',
            'social_services_professional_id' => Professional::factory()->create()->id,
            'probation_officer_professional_id' => Professional::factory()->create()->id,
        ]));

        $response->assertRedirect(route('pupils.show', $pupil));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pupils', [
            'id' => $pupil->id,
            'first_name' => 'Updated',
            'email' => 'updated@example.com',
            'major_id' => $major->id,
            'primary_family_member_id' => $familyMember->id,
            'has_special_needs' => false,
            'special_needs_details' => null,
            'attended_special_school' => false,
            'special_school_details' => null,
            'social_services_involvement' => false,
            'social_services_professional_id' => null,
            'probation_officer_required' => false,
            'probation_officer_professional_id' => null,
        ]);
    }

    public function test_update_treatment_plan_requires_authentication(): void
    {
        $pupil = $this->createPupil();

        $response = $this->post(route('pupils.treatment_plan_updates.store', $pupil), []);

        $response->assertRedirect(route('login'));
    }

    public function test_update_treatment_plan_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('pupils');
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->post(route('pupils.treatment_plan_updates.store', $pupil), [
            'date' => '2026-04-16',
            'description' => 'Reviewed plan.',
        ]);

        $response->assertForbidden();
    }

    public function test_update_treatment_plan_validates_required_fields(): void
    {
        $user = $this->userWithPermissions(['edit-pupils']);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->post(route('pupils.treatment_plan_updates.store', $pupil), []);

        $response->assertSessionHasErrors(['date', 'description']);
    }

    public function test_update_treatment_plan_creates_update(): void
    {
        $user = $this->userWithPermissions(['edit-pupils']);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->post(route('pupils.treatment_plan_updates.store', $pupil), [
            'date' => '2026-04-16',
            'description' => 'Reviewed plan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('treatment_plan_updates', [
            'pupil_id' => $pupil->id,
            'user_id' => $user->id,
            'date' => '2026-04-16 00:00:00',
            'description' => 'Reviewed plan.',
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $pupil = $this->createPupil();
        $response = $this->delete(route('pupils.destroy', $pupil));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('pupils');
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->delete(route('pupils.destroy', $pupil));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_pupil(): void
    {
        $user = $this->userWithPermissions(['delete-pupils']);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->delete(route('pupils.destroy', $pupil));

        $response->assertRedirect(route('pupils.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pupils', ['id' => $pupil->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->userWithPermissions(['delete-pupils']);
        $pupil = $this->createPupil();

        Pupil::deleting(function () {
            throw new QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('pupils.destroy', $pupil));

        $response->assertRedirect(route('pupils.index'));
        $response->assertSessionHas('error');

        Pupil::flushEventListeners();
    }

    public function test_export_downloads_profile_summary(): void
    {
        $user = $this->userWithPermissions(['export-pupil-data']);
        $pupil = $this->createPupil([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $response = $this->actingAs($user)->get(route('pupils.export', $pupil));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pupil_relationships_accessors_and_casts(): void
    {
        $onboarder = $this->userWithPermissions([]);
        $major = Major::factory()->create();
        $socialWorker = Professional::factory()->create();
        $probationOfficer = Professional::factory()->create();
        $sharedProfessional = Professional::factory()->create();
        $recordType = RecordType::factory()->create(['name' => 'Assessment']);
        $meetingType = MeetingType::factory()->create();
        $subject = Subject::factory()->create();
        $proficiency = Proficiency::factory()->create();
        $pupil = Pupil::factory()->create([
            'onboarded_by' => $onboarder->id,
            'major_id' => $major->id,
            'first_name' => 'Test',
            'last_name' => 'Pupil',
            'dob' => '2010-04-16',
            'joined_date' => '2024-09-01',
            'auto_progression' => 1,
            'smoking_history' => 1,
            'drug_abuse_history' => 0,
            'has_special_needs' => 1,
            'attended_special_school' => 0,
            'social_services_involvement' => 1,
            'probation_officer_required' => 1,
            'social_services_professional_id' => $socialWorker->id,
            'probation_officer_professional_id' => $probationOfficer->id,
        ]);
        $familyMember = FamilyMember::factory()->create(['pupil_id' => $pupil->id]);
        $pupil->update(['primary_family_member_id' => $familyMember->id]);
        $medication = Medication::factory()->create(['pupil_id' => $pupil->id]);
        $diagnosis = Diagnosis::factory()->create([
            'pupil_id' => $pupil->id,
            'professional_id' => $sharedProfessional->id,
            'name' => 'Dyslexia',
        ]);
        $record = Record::factory()->create([
            'pupil_id' => $pupil->id,
            'professional_id' => $sharedProfessional->id,
            'record_type_id' => $recordType->id,
        ]);
        $meeting = Meeting::factory()->create([
            'pupil_id' => $pupil->id,
            'meeting_type_id' => $meetingType->id,
        ]);
        $event = Event::factory()->create(['pupil_id' => $pupil->id]);
        $diet = Diet::factory()->create([
            'pupil_id' => $pupil->id,
            'subject_id' => $subject->id,
            'proficiency_id' => $proficiency->id,
        ]);
        $schoolHistory = SchoolHistory::factory()->create(['pupil_id' => $pupil->id]);
        $firstUpdate = TreatmentPlanUpdate::create([
            'pupil_id' => $pupil->id,
            'user_id' => $onboarder->id,
            'date' => '2026-04-15',
            'description' => 'Older update.',
        ]);
        $secondUpdate = TreatmentPlanUpdate::create([
            'pupil_id' => $pupil->id,
            'user_id' => $onboarder->id,
            'date' => '2026-04-16',
            'description' => 'Newer update.',
        ]);
        $initialProgression = PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2025/2026',
            'year_group' => 7,
            'tutor_group' => '7A',
            'type' => 'initial',
        ]);
        $latestProgression = PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8B',
            'type' => 'manual',
        ]);

        $freshPupil = $pupil->fresh();

        $this->assertTrue($freshPupil->onboardedBy->is($onboarder));
        $this->assertTrue($freshPupil->primaryFamilyMember->is($familyMember));
        $this->assertTrue($freshPupil->familyMembers->first()->is($familyMember));
        $this->assertTrue($freshPupil->medications->first()->is($medication));
        $this->assertTrue($freshPupil->diagnoses->first()->is($diagnosis));
        $this->assertTrue($freshPupil->records->first()->is($record));
        $this->assertTrue($freshPupil->meetings->first()->is($meeting));
        $this->assertTrue($freshPupil->events->first()->is($event));
        $this->assertTrue($freshPupil->diets->first()->is($diet));
        $this->assertTrue($freshPupil->schoolHistories->first()->is($schoolHistory));
        $this->assertTrue($freshPupil->socialServicesProfessional->is($socialWorker));
        $this->assertTrue($freshPupil->probationOfficerProfessional->is($probationOfficer));
        $this->assertTrue($freshPupil->major->is($major));
        $this->assertEquals('Test Pupil', $freshPupil->full_name);
        $this->assertEquals('7A', $freshPupil->initial_tutor_group);
        $this->assertEquals('8B', $freshPupil->current_tutor_group);
        $this->assertEquals(8, $freshPupil->current_year_group);
        $this->assertTrue($freshPupil->latestProgression->is($latestProgression));
        $this->assertTrue($freshPupil->progressions->first()->is($initialProgression));
        $this->assertTrue($freshPupil->treatmentPlanUpdates->first()->is($secondUpdate));
        $this->assertTrue($freshPupil->treatmentPlanUpdates->last()->is($firstUpdate));
        $this->assertCount(3, $freshPupil->involvements);
        $this->assertInstanceOf(Carbon::class, $freshPupil->dob);
        $this->assertInstanceOf(Carbon::class, $freshPupil->joined_date);
        $this->assertTrue($freshPupil->auto_progression);
        $this->assertTrue($freshPupil->smoking_history);
        $this->assertFalse($freshPupil->drug_abuse_history);
        $this->assertTrue($freshPupil->has_special_needs);
        $this->assertFalse($freshPupil->attended_special_school);
    }

    public function test_pupil_progression_accessors_return_na_without_progressions(): void
    {
        $pupil = $this->createPupil();

        $this->assertEquals('N/A', $pupil->initial_tutor_group);
        $this->assertEquals('N/A', $pupil->current_tutor_group);
        $this->assertEquals('N/A', $pupil->current_year_group);
    }

    public function test_treatment_plan_update_relationships_and_casts(): void
    {
        $user = $this->userWithPermissions([]);
        $pupil = $this->createPupil();
        $update = TreatmentPlanUpdate::create([
            'pupil_id' => $pupil->id,
            'user_id' => $user->id,
            'date' => '2026-04-16',
            'description' => 'Reviewed plan.',
        ]);

        $this->assertTrue($update->pupil->is($pupil));
        $this->assertTrue($update->user->is($user));
        $this->assertInstanceOf(Carbon::class, $update->date);
    }

    protected function createPupil(array $attributes = []): Pupil
    {
        $onboarder = $this->userWithPermissions([]);

        return Pupil::factory()->create(array_merge([
            'pupil_number' => fake()->unique()->bothify('PUP-###'),
            'onboarded_by' => $onboarder->id,
        ], $attributes));
    }

    protected function pupilPayload(array $overrides = []): array
    {
        return array_merge([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Test',
            'last_name' => 'Pupil',
            'dob' => '2010-04-16',
            'gender' => 'Female',
            'address_line_1' => '1 Test Street',
            'address_line_2' => '',
            'locality' => 'London',
            'postcode' => 'E1 1AA',
            'country' => 'United Kingdom',
            'phone' => '07123456789',
            'email' => 'test@example.com',
            'after_school_job' => 'Tutor',
            'joined_date' => '2024-09-01',
            'major_id' => null,
            'year_group' => 8,
            'tutor_group' => '8A',
            'parental_description' => 'Parent description.',
            'treatment_plan' => 'Current support plan.',
            'has_special_needs' => '1',
            'special_needs_details' => 'Needs support.',
            'attended_special_school' => '1',
            'special_school_details' => 'Previous special school.',
            'smoking_history' => '1',
            'drug_abuse_history' => '1',
            'family_members' => [],
            'school_histories' => [],
            'diagnoses' => [],
            'medications' => [],
            'records' => [],
        ], $overrides);
    }
}
