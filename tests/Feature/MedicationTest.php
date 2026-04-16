<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
        $file = UploadedFile::fake()->create('doc.pdf', 100);

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
}
