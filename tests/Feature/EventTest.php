<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Services\LlmService;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('events.store'), ['title' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('events');
        $response = $this->actingAs($user)->post(route('events.store'), ['title' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_event_and_attachments(): void
    {
        $user = $this->adminUser('events');
        // Ensure pupil exists
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file1 = UploadedFile::fake()->create('doc1.pdf', 100);

        $response = $this->actingAs($user)->post(route('events.store'), [
            'pupil_id' => $pupil->id,
            'title' => 'Meeting',
            'date' => '2026-04-16',
            'reference_number' => 'REF123',
            'additional_attachments' => [$file1],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'pupil_id' => $pupil->id,
            'title' => 'Meeting',
            'reference_number' => 'REF123',
        ]);
        
        $event = Event::where('title', 'Meeting')->first();
        $this->assertCount(1, $event->attachments);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('events');

        $response = $this->actingAs($user)->post(route('events.store'), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'title']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('events');

        $response = $this->actingAs($user)->post(route('events.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_update_modifies_event(): void
    {
        $user = $this->adminUser('events');
        $event = Event::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('new_doc.pdf', 100);

        $response = $this->actingAs($user)->put(route('events.update', $event), [
            'title' => 'Updated Title',
            'date' => '2026-05-01',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Title',
        ]);
        
        $this->assertCount(1, $event->fresh()->attachments);
    }

    public function test_destroy_deletes_event(): void
    {
        $user = $this->adminUser('events');
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete(route('events.destroy', $event));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('events');
        $event = Event::factory()->create();

        Event::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('events.destroy', $event));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Event::flushEventListeners();
    }
}
