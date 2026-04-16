<?php

namespace Tests\Feature;

use App\Models\Professional;
use App\Models\Record;
use App\Models\Pupil;
use App\Models\RecordType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('records.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('records');
        $response = $this->actingAs($user)->post(route('records.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('records');

        $response = $this->actingAs($user)->post(route('records.store'), [
            'title' => 'Missing Fields',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'record_type_id', 'description']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('records');

        $response = $this->actingAs($user)->post(route('records.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_creates_record_and_attachments(): void
    {
        $user = $this->adminUser('records');
        $pupil = Pupil::factory()->create();
        $recordType = RecordType::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $response = $this->actingAs($user)->post(route('records.store'), [
            'pupil_id' => $pupil->id,
            'record_type_id' => $recordType->id,
            'title' => 'Test Record',
            'description' => 'A detailed record description.',
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('records', [
            'pupil_id' => $pupil->id,
            'title' => 'Test Record',
        ]);
        
        $record = Record::where('title', 'Test Record')->first();
        $this->assertCount(1, $record->attachments);
    }

    public function test_store_creates_inline_professional(): void
    {
        $user = $this->adminUser('records');
        $pupil = Pupil::factory()->create();
        $recordType = RecordType::factory()->create();

        $response = $this->actingAs($user)->post(route('records.store'), [
            'pupil_id' => $pupil->id,
            'record_type_id' => $recordType->id,
            'description' => 'Has new professional',
            'is_new_professional' => '1',
            'prof_first_name' => 'John',
            'prof_last_name' => 'Smith',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('professionals', [
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $this->assertDatabaseHas('records', [
            'description' => 'Has new professional',
        ]);
    }

    public function test_update_modifies_record(): void
    {
        $user = $this->adminUser('records');
        $record = Record::factory()->create();
        $newRecordType = RecordType::factory()->create();

        $response = $this->actingAs($user)->put(route('records.update', $record), [
            'record_type_id' => $newRecordType->id,
            'title' => 'Updated Record',
            'description' => 'Updated description.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('records', [
            'id' => $record->id,
            'title' => 'Updated Record',
            'record_type_id' => $newRecordType->id,
        ]);
    }

    public function test_destroy_deletes_record(): void
    {
        $user = $this->adminUser('records');
        $record = Record::factory()->create();

        $response = $this->actingAs($user)->delete(route('records.destroy', $record));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('records', ['id' => $record->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('records');
        $record = Record::factory()->create();

        Record::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('records.destroy', $record));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Record::flushEventListeners();
    }

    public function test_record_relationships_and_date_cast(): void
    {
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create(['onboarded_by' => $onboarder->id]);
        $recordType = RecordType::factory()->create();
        $professional = Professional::factory()->create();
        $record = Record::factory()->create([
            'pupil_id' => $pupil->id,
            'record_type_id' => $recordType->id,
            'professional_id' => $professional->id,
            'date' => '2026-04-16',
        ]);

        $this->assertTrue($record->pupil->is($pupil));
        $this->assertTrue($record->recordType->is($recordType));
        $this->assertTrue($record->professional->is($professional));
        $this->assertInstanceOf(Carbon::class, $record->date);
        $this->assertEquals('2026-04-16', $record->date->format('Y-m-d'));
    }
}
