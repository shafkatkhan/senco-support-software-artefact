<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\AttachmentTranscription;
use App\Models\Diagnosis;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Medication;
use App\Models\Meeting;
use App\Models\Pupil;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\SchoolHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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

    public function test_attachment_relations_source_names_and_delete_cleanup(): void
    {
        Storage::fake('local');
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create([
            'onboarded_by' => $onboarder->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $recordType = RecordType::factory()->create(['name' => 'Assessment']);
        $models = [
            'Medication: Aspirin' => Medication::factory()->create([
                'pupil_id' => $pupil->id,
                'name' => 'Aspirin',
            ]),
            'Diagnosis: Dyslexia' => Diagnosis::factory()->create([
                'pupil_id' => $pupil->id,
                'name' => 'Dyslexia',
            ]),
            'Event: Review Booked' => Event::factory()->create([
                'pupil_id' => $pupil->id,
                'title' => 'Review Booked',
            ]),
            'Meeting: Annual Review' => Meeting::factory()->create([
                'pupil_id' => $pupil->id,
                'title' => 'Annual Review',
            ]),
            'Family Member: Alex Doe' => FamilyMember::factory()->create([
                'pupil_id' => $pupil->id,
                'first_name' => 'Alex',
                'last_name' => 'Doe',
            ]),
            'School History: Central School' => SchoolHistory::factory()->create([
                'pupil_id' => $pupil->id,
                'school_name' => 'Central School',
            ]),
            'Record: Assessment Record' => Record::factory()->create([
                'pupil_id' => $pupil->id,
                'record_type_id' => $recordType->id,
                'title' => null,
            ]),
            'Pupil Profile' => $pupil,
        ];

        foreach ($models as $expectedSourceName => $model) {
            $attachment = Attachment::factory()->create([
                'attachable_type' => $model::class,
                'attachable_id' => $model->id,
            ]);

            $this->assertTrue($attachment->attachable->is($model));
            $this->assertEquals($expectedSourceName, $attachment->source_name);
        }

        $attachment = Attachment::factory()->create([
            'attachable_type' => Pupil::class,
            'attachable_id' => $pupil->id,
            'file_path' => 'attachments/test.pdf',
        ]);
        AttachmentTranscription::create([
            'attachment_id' => $attachment->id,
            'transcript' => 'Transcript text',
        ]);
        Storage::put('attachments/test.pdf', 'contents');

        $this->assertEquals('Transcript text', $attachment->transcription->transcript);

        $attachment->delete();

        Storage::assertMissing('attachments/test.pdf');
        $this->assertEquals('Unknown Source', (new Attachment())->source_name);
    }
}
