<?php

namespace Tests\Feature;

use App\Exports\MedicationsExport;
use App\Http\Controllers\MedicationController;
use App\Models\Pupil;
use App\Traits\ExportsPupilData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportsPupilDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $group = \App\Models\UserGroup::factory()->create();
        \App\Models\User::factory()->create(['user_group_id' => $group->id]);
    }

    public function test_get_pupil_export_filename_formats_pupil_details(): void
    {
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP 001',
            'first_name' => 'Jane',
            'last_name' => 'Doe Smith',
        ]);
        $exporter = new ExportsPupilDataHarness();

        $filename = $exporter->filename($pupil, 'Medications', 'xlsx');

        $this->assertEquals('PUP_001_Jane_Doe_Smith_Medications.xlsx', $filename);
    }

    public function test_download_pupil_export_authorizes_and_downloads_csv(): void
    {
        Excel::fake();
        $user = $this->userWithPermissions(['export-pupil-data']);
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $exporter = new ExportsPupilDataHarness();

        $this->actingAs($user);
        $exporter->download($pupil, MedicationsExport::class, 'Medications', 'csv');

        Excel::assertDownloaded('PUP-001_Jane_Doe_Medications.csv');
    }

    public function test_download_pupil_export_uses_xlsx_for_unknown_format(): void
    {
        Excel::fake();
        $user = $this->userWithPermissions(['export-pupil-data']);
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-002',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $exporter = new ExportsPupilDataHarness();

        $this->actingAs($user);
        $exporter->download($pupil, MedicationsExport::class, 'Medications', 'pdf');

        Excel::assertDownloaded('PUP-002_John_Doe_Medications.xlsx');
    }

    public function test_download_pupil_export_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create();
        $exporter = new ExportsPupilDataHarness();

        $this->actingAs($user);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $exporter->download($pupil, MedicationsExport::class, 'Medications', 'csv');
    }

    public function test_export_spreadsheet_infers_export_class_from_controller_name(): void
    {
        Excel::fake();
        $user = $this->userWithPermissions(['export-pupil-data']);
        $pupil = Pupil::factory()->create([
            'pupil_number' => 'PUP-003',
            'first_name' => 'Alex',
            'last_name' => 'Smith',
        ]);

        $this->actingAs($user);
        (new MedicationController())->exportSpreadsheet($pupil, 'xlsx');

        Excel::assertDownloaded('PUP-003_Alex_Smith_Medications.xlsx', function ($export) {
            return $export instanceof MedicationsExport;
        });
    }
}

class ExportsPupilDataHarness
{
    use ExportsPupilData;

    public function filename(Pupil $pupil, string $suffix, string $extension): string
    {
        return $this->getPupilExportFilename($pupil, $suffix, $extension);
    }

    public function download(Pupil $pupil, string $exportClass, string $suffix, string $format)
    {
        return $this->downloadPupilExport($pupil, $exportClass, $suffix, $format);
    }
}
