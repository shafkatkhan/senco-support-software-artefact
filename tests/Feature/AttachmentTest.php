<?php

namespace Tests\Feature;

use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Transcription;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_requires_authentication(): void
    {
        $attachment = Attachment::factory()->create();
        $response = $this->get(route('attachments.show', $attachment));
        $response->assertRedirect(route('login'));
    }

    public function test_show_returns_404_if_file_missing(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create(['file_path' => 'missing.txt']);
        
        Storage::fake('local');
        
        $response = $this->actingAs($user)->get(route('attachments.show', $attachment));
        $response->assertNotFound();
    }

    public function test_show_serves_file(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create([
            'file_path' => 'attachments/test.txt',
            'filename' => 'test.txt',
            'mime_type' => 'text/plain',
        ]);
        
        Storage::fake('local');
        Storage::put('attachments/test.txt', 'Content');
        
        $response = $this->actingAs($user)->get(route('attachments.show', $attachment));
        
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    public function test_show_downloads_file_when_requested(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create([
            'file_path' => 'attachments/test.txt',
            'filename' => 'test.txt',
            'mime_type' => 'text/plain',
        ]);
        
        Storage::fake('local');
        Storage::put('attachments/test.txt', 'Content');
        
        $response = $this->actingAs($user)->get(route('attachments.show', ['attachment' => $attachment, 'download' => 1]));
        
        $response->assertOk();
        $response->assertDownload('test.txt');
    }

    public function test_update_transcript_updates_record(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create();
        \App\Models\AttachmentTranscription::create([
            'attachment_id' => $attachment->id,
            'transcript' => 'Old text',
        ]);
        
        $response = $this->actingAs($user)->put(route('attachments.update_transcript', $attachment), [
            'transcript' => 'New text',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('New text', $attachment->fresh()->transcription->transcript);
    }

    public function test_destroy_deletes_attachment(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('attachments.destroy', $attachment));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->userWithPermissions(['manage-attachments']);
        $attachment = Attachment::factory()->create();
        
        Attachment::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });
        
        $response = $this->actingAs($user)->delete(route('attachments.destroy', $attachment));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Attachment::flushEventListeners();
    }
}
