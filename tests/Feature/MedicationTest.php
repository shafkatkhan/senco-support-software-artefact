<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('medications.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('medications');
        $response = $this->actingAs($user)->post(route('medications.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('medications');

        $response = $this->actingAs($user)->post(route('medications.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'name', 'start_date']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('medications');

        $response = $this->actingAs($user)->post(route('medications.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_creates_medication_and_attachments(): void
    {
        $user = $this->adminUser('medications');
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('doc.pdf', '%PDF-1.4 test');

        $response = $this->actingAs($user)->post(route('medications.store'), [
            'pupil_id' => $pupil->id,
            'name' => 'Aspirin',
            'dosage' => '10mg',
            'frequency' => 'Daily',
            'start_date' => '2026-04-16',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medications', [
            'pupil_id' => $pupil->id,
            'name' => 'Aspirin',
            'dosage' => '10mg',
        ]);
        
        $med = Medication::where('name', 'Aspirin')->first();
        $this->assertCount(1, $med->attachments);
    }

    public function test_update_modifies_medication(): void
    {
        $user = $this->adminUser('medications');
        $med = Medication::factory()->create();

        $response = $this->actingAs($user)->put(route('medications.update', $med), [
            'name' => 'Ibuprofen',
            'dosage' => '20mg',
            'start_date' => '2026-04-16',
            'frequency' => 'Daily',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medications', [
            'id' => $med->id,
            'name' => 'Ibuprofen',
        ]);
    }

    public function test_destroy_deletes_medication(): void
    {
        $user = $this->adminUser('medications');
        $med = Medication::factory()->create();

        $response = $this->actingAs($user)->delete(route('medications.destroy', $med));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('medications', ['id' => $med->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('medications');
        $med = Medication::factory()->create();

        Medication::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('medications.destroy', $med));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Medication::flushEventListeners();
    }

    public function test_medication_belongs_to_pupil_and_casts_dates_and_boolean(): void
    {
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create(['onboarded_by' => $onboarder->id]);
        $medication = Medication::factory()->create([
            'pupil_id' => $pupil->id,
            'start_date' => '2026-04-16',
            'end_date' => '2026-05-16',
            'expiry_date' => '2027-04-16',
            'self_administer' => 1,
        ]);

        $this->assertTrue($medication->pupil->is($pupil));
        $this->assertInstanceOf(Carbon::class, $medication->start_date);
        $this->assertInstanceOf(Carbon::class, $medication->end_date);
        $this->assertInstanceOf(Carbon::class, $medication->expiry_date);
        $this->assertEquals('2026-04-16', $medication->start_date->format('Y-m-d'));
        $this->assertEquals('2026-05-16', $medication->end_date->format('Y-m-d'));
        $this->assertEquals('2027-04-16', $medication->expiry_date->format('Y-m-d'));
        $this->assertTrue($medication->self_administer);
    }
}
