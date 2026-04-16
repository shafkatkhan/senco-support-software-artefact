<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('meetings.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('meetings');
        $response = $this->actingAs($user)->post(route('meetings.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('meetings');

        $response = $this->actingAs($user)->post(route('meetings.store'), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'pupil_id']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('meetings');

        $response = $this->actingAs($user)->post(route('meetings.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_creates_meeting_and_attachments(): void
    {
        $user = $this->adminUser('meetings');
        $type = MeetingType::factory()->create();
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $response = $this->actingAs($user)->post(route('meetings.store'), [
            'pupil_id' => $pupil->id,
            'meeting_type_id' => $type->id,
            'title' => 'Annual Review',
            'date' => '2026-05-01',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meetings', [
            'meeting_type_id' => $type->id,
            'title' => 'Annual Review',
        ]);
        
        $meeting = Meeting::where('title', 'Annual Review')->first();
        $this->assertCount(1, $meeting->attachments);
    }

    public function test_update_modifies_meeting(): void
    {
        $user = $this->adminUser('meetings');
        $meeting = Meeting::factory()->create();
        $newType = MeetingType::factory()->create();

        $response = $this->actingAs($user)->put(route('meetings.update', $meeting), [
            'meeting_type_id' => $newType->id,
            'title' => 'Updated Title',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'meeting_type_id' => $newType->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_destroy_deletes_meeting(): void
    {
        $user = $this->adminUser('meetings');
        $meeting = Meeting::factory()->create();

        $response = $this->actingAs($user)->delete(route('meetings.destroy', $meeting));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('meetings');
        $meeting = Meeting::factory()->create();

        Meeting::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('meetings.destroy', $meeting));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Meeting::flushEventListeners();
    }

    public function test_meeting_relationships_and_date_cast(): void
    {
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create(['onboarded_by' => $onboarder->id]);
        $meetingType = MeetingType::factory()->create();
        $meeting = Meeting::factory()->create([
            'pupil_id' => $pupil->id,
            'meeting_type_id' => $meetingType->id,
            'date' => '2026-04-16',
        ]);

        $this->assertTrue($meeting->pupil->is($pupil));
        $this->assertTrue($meeting->meetingType->is($meetingType));
        $this->assertInstanceOf(Carbon::class, $meeting->date);
        $this->assertEquals('2026-04-16', $meeting->date->format('Y-m-d'));
    }
}
