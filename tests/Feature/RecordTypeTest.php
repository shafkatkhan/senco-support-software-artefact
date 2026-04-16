<?php

namespace Tests\Feature;

use App\Models\Record;
use App\Models\RecordType;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('record-types.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->get(route('record-types.index'));

        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('record-types');
        RecordType::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('record-types.index'));

        $response->assertOk();
        $response->assertViewIs('record_types');
        $response->assertViewHas('record_types');
    }

    public function test_index_lists_all_record_types(): void
    {
        $user = $this->viewerUser('record-types');
        $types = RecordType::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('record-types.index'));

        $response->assertSee($types[0]->name);
        $response->assertSee($types[1]->name);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('record-types.store'), ['name' => 'Test']);

        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('record-types');

        $response = $this->actingAs($user)->post(route('record-types.store'), ['name' => 'Test']);

        $response->assertForbidden();
    }

    public function test_store_creates_record_type(): void
    {
        $user = $this->adminUser('record-types');

        $this->actingAs($user)->post(route('record-types.store'), [
            'name' => 'New Type',
            'description' => 'A description',
        ]);

        $this->assertDatabaseHas('record_types', [
            'name' => 'New Type',
            'description' => 'A description',
        ]);
    }

    public function test_store_redirects_back_on_success(): void
    {
        $user = $this->adminUser('record-types');

        $response = $this->actingAs($user)->post(route('record-types.store'), [
            'name' => 'Another Type',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_store_validates_name_is_required(): void
    {
        $user = $this->adminUser('record-types');

        $response = $this->actingAs($user)->post(route('record-types.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('record_types', 0);
    }

    public function test_store_validates_name_is_unique(): void
    {
        $user = $this->adminUser('record-types');
        RecordType::factory()->create(['name' => 'Duplicate']);

        $response = $this->actingAs($user)->post(route('record-types.store'), [
            'name' => 'Duplicate',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('record_types', 1);
    }

    public function test_store_allows_nullable_description(): void
    {
        $user = $this->adminUser('record-types');

        $this->actingAs($user)->post(route('record-types.store'), [
            'name' => 'No Desc Type',
            'description' => '',
        ]);

        $this->assertDatabaseHas('record_types', ['name' => 'No Desc Type']);
    }

    public function test_update_requires_authentication(): void
    {
        $type = RecordType::factory()->create();

        $response = $this->put(route('record-types.update', $type), ['name' => 'Changed']);

        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('record-types');
        $type = RecordType::factory()->create();

        $response = $this->actingAs($user)->put(route('record-types.update', $type), ['name' => 'Changed']);

        $response->assertForbidden();
    }

    public function test_update_modifies_existing_record_type(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create(['name' => 'Old Name', 'description' => 'Old desc']);

        $this->actingAs($user)->put(route('record-types.update', $type), [
            'name' => 'New Name',
            'description' => 'New desc',
        ]);

        $this->assertDatabaseHas('record_types', ['id' => $type->id, 'name' => 'New Name', 'description' => 'New desc']);
    }

    public function test_update_redirects_back_on_success(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();

        $response = $this->actingAs($user)->put(route('record-types.update', $type), [
            'name' => 'Updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_update_validates_name_is_required(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($user)->put(route('record-types.update', $type), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('record_types', ['id' => $type->id, 'name' => 'Original']);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create(['name' => 'Same Name']);

        $response = $this->actingAs($user)->put(route('record-types.update', $type), [
            'name' => 'Same Name',
            'description' => 'Updated description',
        ]);

        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('record_types', ['id' => $type->id, 'description' => 'Updated description']);
    }

    public function test_update_validates_name_unique_across_other_records(): void
    {
        $user = $this->adminUser('record-types');
        RecordType::factory()->create(['name' => 'Taken']);
        $type = RecordType::factory()->create(['name' => 'Mine']);

        $response = $this->actingAs($user)->put(route('record-types.update', $type), [
            'name' => 'Taken',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_destroy_requires_authentication(): void
    {
        $type = RecordType::factory()->create();

        $response = $this->delete(route('record-types.destroy', $type));

        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('record-types');
        $type = RecordType::factory()->create();

        $response = $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $response->assertForbidden();
    }

    public function test_destroy_deletes_record_type(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();

        $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $this->assertDatabaseMissing('record_types', ['id' => $type->id]);
    }

    public function test_destroy_redirects_back_on_success(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();

        $response = $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_destroy_also_deletes_associated_records_via_cascade_or_fails_with_query_exception(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();
        $pupil = Pupil::factory()->create(['onboarded_by' => $user->id]);

        $record = Record::factory()->create([
            'record_type_id' => $type->id,
            'pupil_id' => $pupil->id,
        ]);

        $response = $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $response->assertRedirect();
        
        $typeExists = RecordType::where('id', $type->id)->exists();
        if ($typeExists) {
            $response->assertSessionHas('error');
            $this->assertDatabaseHas('record_types', ['id' => $type->id]);
            $this->assertDatabaseHas('records', ['id' => $record->id]);
        } else {
            $response->assertSessionHas('success');
            $this->assertDatabaseMissing('record_types', ['id' => $type->id]);
            $this->assertDatabaseMissing('records', ['id' => $record->id]);
        }
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();

        RecordType::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        RecordType::flushEventListeners();
    }

    public function test_destroy_prevents_deletion_if_records_assigned(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();

        RecordType::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception('fk', 23000));
        });

        $response = $this->actingAs($user)->delete(route('record-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        RecordType::flushEventListeners();
    }

    public function test_record_type_has_many_records(): void
    {
        $user = $this->adminUser('record-types');
        $type = RecordType::factory()->create();
        $pupil = Pupil::factory()->create(['onboarded_by' => $user->id]);

        Record::factory()->count(2)->create([
            'record_type_id' => $type->id,
            'pupil_id' => $pupil->id,
        ]);

        $this->assertCount(2, $type->records);
    }
}
