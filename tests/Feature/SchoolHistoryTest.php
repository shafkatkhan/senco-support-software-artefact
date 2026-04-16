<?php

namespace Tests\Feature;

use App\Models\SchoolHistory;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('school-histories.store'), ['school_name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('school-histories');
        $response = $this->actingAs($user)->post(route('school-histories.store'), ['school_name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_history_and_attachments(): void
    {
        $user = $this->adminUser('school-histories');
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file1 = UploadedFile::fake()->create('doc1.pdf', 100);

        $response = $this->actingAs($user)->post(route('school-histories.store'), [
            'pupil_id' => $pupil->id,
            'school_name' => 'High School',
            'school_type' => 'State',
            'years_attended' => 5,
            'additional_attachments' => [$file1],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('school_histories', [
            'pupil_id' => $pupil->id,
            'school_name' => 'High School',
            'years_attended' => 5,
        ]);
        
        $history = SchoolHistory::where('school_name', 'High School')->first();
        $this->assertCount(1, $history->attachments);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('school-histories');

        $response = $this->actingAs($user)->post(route('school-histories.store'), [
            'school_name' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'school_name']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('school-histories');

        $response = $this->actingAs($user)->post(route('school-histories.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_update_modifies_history(): void
    {
        $user = $this->adminUser('school-histories');
        $history = SchoolHistory::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('new_doc.pdf', 100);

        $response = $this->actingAs($user)->put(route('school-histories.update', $history), [
            'school_name' => 'Updated School',
            'school_type' => 'Grammar',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('school_histories', [
            'id' => $history->id,
            'school_name' => 'Updated School',
            'school_type' => 'Grammar',
        ]);
        
        $this->assertCount(1, $history->fresh()->attachments);
    }

    public function test_destroy_deletes_history(): void
    {
        $user = $this->adminUser('school-histories');
        $history = SchoolHistory::factory()->create();

        $response = $this->actingAs($user)->delete(route('school-histories.destroy', $history));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('school_histories', ['id' => $history->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('school-histories');
        $history = SchoolHistory::factory()->create();

        SchoolHistory::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('school-histories.destroy', $history));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        SchoolHistory::flushEventListeners();
    }
}
