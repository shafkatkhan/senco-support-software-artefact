<?php

namespace Tests\Feature;

use App\Exports\BaseExport;
use App\Exports\DiagnosesExport;
use App\Exports\DietsExport;
use App\Exports\EventsExport;
use App\Exports\FamilyMembersExport;
use App\Exports\MedicationsExport;
use App\Exports\MeetingsExport;
use App\Exports\PupilProgressionsExport;
use App\Exports\RecordsExport;
use App\Exports\ReportsExport;
use App\Exports\SchoolHistoriesExport;
use App\Models\Accommodation;
use App\Models\Diagnosis;
use App\Models\Diet;
use App\Models\Event;
use App\Models\FamilyMember;
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
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ExportClassesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $group = \App\Models\UserGroup::factory()->create();
        \App\Models\User::factory()->create(['user_group_id' => $group->id]);
    }

    public function test_base_export_csv_settings_styles_and_after_sheet_event(): void
    {
        config(['app.language_direction' => 'rtl']);
        app()->setLocale('en');

        $export = new TestBaseExport();
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray([
            ['Heading A', 'Heading B'],
            [str_repeat('Long value ', 40), 'Value B'],
        ]);
        foreach (['A', 'B'] as $column) {
            $worksheet->getColumnDimension($column)->setAutoSize(true);
        }

        $events = $export->registerEvents();
        $events[AfterSheet::class](new AfterSheet(new Sheet($worksheet), $export));

        $this->assertEquals(['use_bom' => true], $export->getCsvSettings());
        $this->assertEquals([1 => ['font' => ['bold' => true, 'underline' => true]]], $export->styles($worksheet));
        $this->assertTrue($worksheet->getRightToLeft());
        $this->assertTrue($worksheet->getStyle('A1:B2')->getAlignment()->getWrapText());
        $this->assertFalse($worksheet->getColumnDimension('A')->getAutoSize());
        $this->assertEquals(50.0, $worksheet->getColumnDimension('A')->getWidth());
    }

    public function test_medications_export_collects_and_maps_rows(): void
    {
        $pupil = Pupil::factory()->create();
        $medication = Medication::factory()->create([
            'pupil_id' => $pupil->id,
            'name' => 'Salbutamol',
            'dosage' => '2 puffs',
            'self_administer' => true,
        ]);

        $export = new MedicationsExport($pupil);

        $this->assertTrue($export->collection()->contains($medication));
        $this->assertContains('Dosage', $export->headings());
        $this->assertEquals('Salbutamol', $export->map($medication)[0]);
        $this->assertEquals('Yes', $export->map($medication)[9]);
    }

    public function test_diagnoses_export_maps_professional_name(): void
    {
        $pupil = Pupil::factory()->create();
        $professional = Professional::factory()->create([
            'title' => 'Dr',
            'first_name' => 'Sam',
            'last_name' => 'Taylor',
        ]);
        $diagnosis = Diagnosis::factory()->create([
            'pupil_id' => $pupil->id,
            'professional_id' => $professional->id,
            'name' => 'ADHD',
            'date' => '2026-04-16',
        ]);

        $export = new DiagnosesExport($pupil);

        $this->assertTrue($export->collection()->contains($diagnosis));
        $this->assertContains('Carried Out By', $export->headings());
        $this->assertEquals('ADHD', $export->map($diagnosis)[0]);
        $this->assertEquals('Dr Sam Taylor', $export->map($diagnosis)[4]);
    }

    public function test_records_export_maps_type_and_professional_name(): void
    {
        $pupil = Pupil::factory()->create();
        $type = RecordType::factory()->create(['name' => 'Assessment']);
        $professional = Professional::factory()->create([
            'title' => 'Ms',
            'first_name' => 'Rita',
            'last_name' => 'Jones',
        ]);
        $record = Record::factory()->create([
            'pupil_id' => $pupil->id,
            'record_type_id' => $type->id,
            'professional_id' => $professional->id,
            'title' => 'Initial Assessment',
        ]);

        $export = new RecordsExport($pupil);

        $this->assertTrue($export->collection()->contains($record));
        $this->assertContains('Professional', $export->headings());
        $this->assertEquals('Assessment', $export->map($record)[1]);
        $this->assertEquals('Ms Rita Jones', $export->map($record)[4]);
    }

    public function test_events_export_orders_and_maps_rows(): void
    {
        $pupil = Pupil::factory()->create();
        Event::factory()->create(['pupil_id' => $pupil->id, 'title' => 'Older', 'date' => '2026-01-01']);
        $latest = Event::factory()->create(['pupil_id' => $pupil->id, 'title' => 'Latest', 'date' => '2026-04-16']);

        $export = new EventsExport($pupil);

        $this->assertEquals($latest->id, $export->collection()->first()->id);
        $this->assertContains('Reference No.', $export->headings());
        $this->assertEquals('Latest', $export->map($latest)[0]);
    }

    public function test_family_members_export_maps_next_of_kin(): void
    {
        $pupil = Pupil::factory()->create();
        $familyMember = FamilyMember::factory()->create([
            'pupil_id' => $pupil->id,
            'first_name' => 'Mary',
            'last_name' => 'Doe',
            'relation' => 'Parent',
        ]);
        $pupil->update(['primary_family_member_id' => $familyMember->id]);

        $export = new FamilyMembersExport($pupil);

        $this->assertTrue($export->collection()->contains($familyMember));
        $this->assertContains('Next of Kin?', $export->headings());
        $this->assertEquals('Mary', $export->map($familyMember->fresh())[0]);
        $this->assertEquals('Yes', $export->map($familyMember->fresh())[16]);
    }

    public function test_meetings_export_maps_meeting_type(): void
    {
        $pupil = Pupil::factory()->create();
        $type = MeetingType::factory()->create(['name' => 'Review']);
        $meeting = Meeting::factory()->create([
            'pupil_id' => $pupil->id,
            'meeting_type_id' => $type->id,
            'title' => 'Termly Review',
        ]);

        $export = new MeetingsExport($pupil);

        $this->assertTrue($export->collection()->contains($meeting));
        $this->assertContains('Participants', $export->headings());
        $this->assertEquals('Review', $export->map($meeting->fresh('meetingType'))[1]);
    }

    public function test_diets_export_maps_subject_proficiency_and_accommodations(): void
    {
        $pupil = Pupil::factory()->create();
        $subject = Subject::factory()->create(['name' => 'Maths']);
        $proficiency = Proficiency::factory()->create(['name' => 'Advanced']);
        $diet = Diet::factory()->create([
            'pupil_id' => $pupil->id,
            'subject_id' => $subject->id,
            'proficiency_id' => $proficiency->id,
        ]);
        $accommodation = Accommodation::factory()->create(['name' => 'Extra Time']);
        $diet->accommodations()->attach($accommodation, [
            'status' => 'Approved',
            'details' => '25 percent extra time',
        ]);

        $export = new DietsExport($pupil);
        $mapped = $export->map($diet->fresh(['subject', 'proficiency', 'accommodations']));

        $this->assertTrue($export->collection()->contains($diet));
        $this->assertContains('Accommodations', $export->headings());
        $this->assertEquals('Maths', $mapped[0]);
        $this->assertEquals('Advanced', $mapped[1]);
        $this->assertStringContainsString('Extra Time (Approved)', $mapped[2]);
        $this->assertStringContainsString('25 percent extra time', $mapped[2]);
    }

    public function test_school_histories_export_maps_years_attended(): void
    {
        $pupil = Pupil::factory()->create();
        $history = SchoolHistory::factory()->create([
            'pupil_id' => $pupil->id,
            'school_name' => 'Example School',
            'years_attended' => 2,
        ]);

        $export = new SchoolHistoriesExport($pupil);

        $this->assertTrue($export->collection()->contains($history));
        $this->assertContains('Years Attended', $export->headings());
        $this->assertEquals('2.0', $export->map($history)[3]);
    }

    public function test_pupil_progressions_export_maps_year_and_type(): void
    {
        $pupil = Pupil::factory()->create();
        $progression = PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => '10',
            'tutor_group' => '10A',
            'type' => 'initial',
        ]);

        $export = new PupilProgressionsExport($pupil);

        $this->assertTrue($export->collection()->contains($progression));
        $this->assertContains('Academic Year', $export->headings());
        $this->assertEquals('Year 10', $export->map($progression)[1]);
        $this->assertEquals('Initial', $export->map($progression)[3]);
    }

    public function test_reports_export_maps_pupil_summary_data(): void
    {
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-100',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'phone' => null,
            'email' => null,
            'auto_progression' => true,
        ]);
        $socialWorker = Professional::factory()->create([
            'first_name' => 'Sara',
            'last_name' => 'Social',
            'phone' => '07111111111',
            'email' => 'sara@example.com',
        ]);
        $probationOfficer = Professional::factory()->create([
            'first_name' => 'Paul',
            'last_name' => 'Probation',
            'phone' => '07222222222',
            'email' => 'paul@example.com',
        ]);
        $pupil->update([
            'social_services_professional_id' => $socialWorker->id,
            'probation_officer_professional_id' => $probationOfficer->id,
        ]);
        Medication::factory()->create(['pupil_id' => $pupil->id, 'name' => 'Salbutamol']);
        Diagnosis::factory()->create(['pupil_id' => $pupil->id, 'name' => 'ADHD']);
        $subject = Subject::factory()->create(['name' => 'English']);
        $diet = Diet::factory()->create(['pupil_id' => $pupil->id, 'subject_id' => $subject->id]);
        $accommodation = Accommodation::factory()->create(['name' => 'Reader']);
        $diet->accommodations()->attach($accommodation, ['status' => 'Approved']);
        PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => '10',
            'tutor_group' => '10A',
            'type' => 'initial',
        ]);

        $relations = [
            'socialServicesProfessional',
            'probationOfficerProfessional',
            'medications',
            'diagnoses',
            'diets.subject',
            'diets.accommodations',
            'latestProgression',
        ];
        $export = new ReportsExport(new Collection([$pupil->fresh($relations)]));
        $mapped = $export->map($pupil->fresh($relations));

        $this->assertCount(1, $export->collection());
        $this->assertContains('Pupil Number', $export->headings());
        $this->assertEquals('PUP-100', $mapped[0]);
        $this->assertEquals('Jane', $mapped[1]);
        $this->assertEquals('Yes', $mapped[14]);
        $this->assertEquals('10', $mapped[32]);
        $this->assertEquals('10A', $mapped[33]);
        $this->assertEquals('ADHD', $mapped[34]);
        $this->assertEquals('Salbutamol', $mapped[35]);
        $this->assertEquals('English', $mapped[36]);
        $this->assertEquals('Reader (Approved for English)', $mapped[37]);
    }
}

class TestBaseExport extends BaseExport
{
}
