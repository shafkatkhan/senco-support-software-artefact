<?php

namespace Tests\Feature;

use App\Exports\PupilProgressionsExport;
use App\Models\Pupil;
use App\Models\PupilProgression;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PupilProgressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('pupil-progressions.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->post(route('pupil-progressions.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);

        $response = $this->actingAs($user)->post(route('pupil-progressions.store'), [
            'academic_year' => '2026',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'academic_year', 'year_group']);
    }

    public function test_store_creates_manual_progression(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->post(route('pupil-progressions.store'), [
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pupil_progressions', [
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->put(route('pupil-progressions.update', $progression), []);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->actingAs($user)->put(route('pupil-progressions.update', $progression), []);
        $response->assertForbidden();
    }

    public function test_update_modifies_progression(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);

        $response = $this->actingAs($user)->put(route('pupil-progressions.update', $progression), [
            'academic_year' => '2027/2028',
            'year_group' => 9,
            'tutor_group' => '9B',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pupil_progressions', [
            'id' => $progression->id,
            'academic_year' => '2027/2028',
            'year_group' => 9,
            'tutor_group' => '9B',
            'type' => 'manual',
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->delete(route('pupil-progressions.destroy', $progression));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->actingAs($user)->delete(route('pupil-progressions.destroy', $progression));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_progression(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->actingAs($user)->delete(route('pupil-progressions.destroy', $progression));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('pupil_progressions', ['id' => $progression->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);
        $progression = PupilProgression::create([
            'pupil_id' => $this->createPupil()->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        PupilProgression::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('pupil-progressions.destroy', $progression));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        PupilProgression::flushEventListeners();
    }

    public function test_toggle_auto_progression_requires_authentication(): void
    {
        $pupil = $this->createPupil();

        $response = $this->put(route('pupils.toggle-auto-progression', $pupil), []);
        $response->assertRedirect(route('login'));
    }

    public function test_toggle_auto_progression_updates_pupil(): void
    {
        $user = $this->userWithPermissions(['manage-pupil-progressions']);
        $pupil = $this->createPupil(['auto_progression' => false]);

        $response = $this->actingAs($user)->put(route('pupils.toggle-auto-progression', $pupil), [
            'auto_progression' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue($pupil->fresh()->auto_progression);
    }

    public function test_export_requires_authentication(): void
    {
        $pupil = $this->createPupil();

        $response = $this->get(route('pupils.pupil-progressions.export', [$pupil, 'csv']));
        $response->assertRedirect(route('login'));
    }

    public function test_export_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $pupil = $this->createPupil();

        $response = $this->actingAs($user)->get(route('pupils.pupil-progressions.export', [$pupil, 'csv']));
        $response->assertForbidden();
    }

    public function test_export_downloads_progressions(): void
    {
        Excel::fake();
        $user = $this->userWithPermissions(['export-pupil-data']);
        $pupil = $this->createPupil([
            'pupil_number' => 'PUP-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'type' => 'manual',
        ]);

        $response = $this->actingAs($user)->get(route('pupils.pupil-progressions.export', [$pupil, 'xlsx']));

        $response->assertOk();
        Excel::assertDownloaded('PUP-001_Jane_Doe_PupilProgressions.xlsx', function ($export) {
            return $export instanceof PupilProgressionsExport;
        });
    }

    public function test_export_maps_progression_rows(): void
    {
        $pupil = $this->createPupil();
        $progression = PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);
        $progression->created_at = Carbon::parse('2026-04-16 10:30:00');
        $progression->updated_at = Carbon::parse('2026-04-16 11:30:00');
        $progression->timestamps = false;
        $progression->save();
        $export = new PupilProgressionsExport($pupil);

        $this->assertEquals([
            '2026/2027',
            'Year 8',
            '8A',
            'Manual',
            '16/04/2026, 10:30',
            '16/04/2026, 11:30',
        ], $export->map($progression->fresh()));
    }

    public function test_pupil_progression_belongs_to_pupil(): void
    {
        $pupil = $this->createPupil();
        $progression = PupilProgression::create([
            'pupil_id' => $pupil->id,
            'academic_year' => '2026/2027',
            'year_group' => 8,
            'tutor_group' => '8A',
            'type' => 'manual',
        ]);

        $this->assertTrue($progression->pupil->is($pupil));
    }

    public function test_pupil_progression_allows_expected_mass_assignment(): void
    {
        $progression = new PupilProgression();

        $this->assertTrue($progression->isFillable('pupil_id'));
        $this->assertTrue($progression->isFillable('academic_year'));
        $this->assertTrue($progression->isFillable('year_group'));
        $this->assertTrue($progression->isFillable('tutor_group'));
        $this->assertTrue($progression->isFillable('type'));
    }

    protected function createPupil(array $attributes = []): Pupil
    {
        $onboarder = $this->userWithPermissions([]);

        return Pupil::factory()->create(array_merge([
            'onboarded_by' => $onboarder->id,
        ], $attributes));
    }
}
