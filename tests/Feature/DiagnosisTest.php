<?php

namespace Tests\Feature;

use App\Models\Diagnosis;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiagnosisTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('diagnoses.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('diagnoses');
        $response = $this->actingAs($user)->post(route('diagnoses.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('diagnoses');

        $response = $this->actingAs($user)->post(route('diagnoses.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'name']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('diagnoses');

        $response = $this->actingAs($user)->post(route('diagnoses.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_creates_diagnosis_and_attachments(): void
    {
        $user = $this->adminUser('diagnoses');
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $response = $this->actingAs($user)->post(route('diagnoses.store'), [
            'pupil_id' => $pupil->id,
            'name' => 'ADHD',
            'status' => 'Confirmed',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('diagnoses', [
            'pupil_id' => $pupil->id,
            'name' => 'ADHD',
        ]);
        
        $diag = Diagnosis::where('name', 'ADHD')->first();
        $this->assertCount(1, $diag->attachments);
    }

    public function test_update_modifies_diagnosis(): void
    {
        $user = $this->adminUser('diagnoses');
        $diag = Diagnosis::factory()->create();

        $response = $this->actingAs($user)->put(route('diagnoses.update', $diag), [
            'name' => 'Autism',
            'status' => 'Pending',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('diagnoses', [
            'id' => $diag->id,
            'name' => 'Autism',
        ]);
    }

    public function test_destroy_deletes_diagnosis(): void
    {
        $user = $this->adminUser('diagnoses');
        $diag = Diagnosis::factory()->create();

        $response = $this->actingAs($user)->delete(route('diagnoses.destroy', $diag));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('diagnoses', ['id' => $diag->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('diagnoses');
        $diag = Diagnosis::factory()->create();

        Diagnosis::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('diagnoses.destroy', $diag));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Diagnosis::flushEventListeners();
    }
}
