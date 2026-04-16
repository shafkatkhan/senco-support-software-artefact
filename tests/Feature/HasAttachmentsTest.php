<?php

namespace Tests\Feature;

use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HasAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $group = \App\Models\UserGroup::factory()->create();
        \App\Models\User::factory()->create(['user_group_id' => $group->id]);
    }

    public function test_attachments_returns_morph_many_relationship(): void
    {
        $pupil = Pupil::factory()->create();

        $attachment = $pupil->attachments()->create([
            'filename' => 'support-plan.pdf',
            'file_path' => 'attachments/support-plan.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1234,
        ]);

        $this->assertTrue($pupil->attachments->contains($attachment));
        $this->assertEquals(Pupil::class, $attachment->attachable_type);
        $this->assertEquals($pupil->id, $attachment->attachable_id);
    }

    public function test_save_attachments_does_nothing_without_files(): void
    {
        $pupil = Pupil::factory()->create();

        $pupil->saveAttachments(null);

        $this->assertDatabaseCount('attachments', 0);
    }

    public function test_save_attachments_stores_a_single_file(): void
    {
        Storage::fake('local');
        $pupil = Pupil::factory()->create();
        $file = UploadedFile::fake()->create('evidence.pdf', 12, 'application/pdf');

        $pupil->saveAttachments($file);

        $attachment = $pupil->attachments()->first();
        $this->assertEquals('evidence.pdf', $attachment->filename);
        $this->assertEquals('application/pdf', $attachment->mime_type);
        $this->assertNotNull($attachment->size_bytes);
        Storage::assertExists($attachment->file_path);
    }

    public function test_save_attachments_stores_multiple_files_with_transcripts(): void
    {
        Storage::fake('local');
        $pupil = Pupil::factory()->create();

        $pupil->saveAttachments([
            UploadedFile::fake()->create('first.txt', 1, 'text/plain'),
            UploadedFile::fake()->create('second.txt', 1, 'text/plain'),
        ], 'Extracted transcript');

        $this->assertEquals(2, $pupil->attachments()->count());

        foreach ($pupil->attachments as $attachment) {
            Storage::assertExists($attachment->file_path);
            $this->assertEquals('Extracted transcript', $attachment->transcription->transcript);
        }
    }

    public function test_save_llm_attachment_stores_file_with_transcript(): void
    {
        Storage::fake('local');
        $pupil = Pupil::factory()->create();
        $file = UploadedFile::fake()->create('audio.mp3', 5, 'audio/mpeg');

        $pupil->saveLlmAttachment($file, 'Spoken notes');

        $attachment = $pupil->attachments()->first();
        $this->assertEquals('audio.mp3', $attachment->filename);
        $this->assertEquals('Spoken notes', $attachment->transcription->transcript);
        Storage::assertExists($attachment->file_path);
    }

    public function test_deleting_model_deletes_attachments_and_files(): void
    {
        Storage::fake('local');
        $pupil = Pupil::factory()->create();
        Storage::put('attachments/report.pdf', 'content');

        $attachment = $pupil->attachments()->create([
            'filename' => 'report.pdf',
            'file_path' => 'attachments/report.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 7,
        ]);

        $pupil->delete();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::assertMissing('attachments/report.pdf');
    }
}
