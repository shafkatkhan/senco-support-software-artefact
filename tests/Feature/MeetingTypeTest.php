<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\Permission;
use App\Models\Pupil;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('meeting-types.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->get(route('meeting-types.index'));

        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('meeting-types');
        MeetingType::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('meeting-types.index'));

        $response->assertOk();
        $response->assertViewIs('meeting_types');
        $response->assertViewHas('meeting_types');
    }

    public function test_index_lists_all_meeting_types(): void
    {
        $user = $this->viewerUser('meeting-types');
        $types = MeetingType::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('meeting-types.index'));

        $response->assertSee($types[0]->name);
        $response->assertSee($types[1]->name);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('meeting-types.store'), ['name' => 'Test']);

        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('meeting-types');

        $response = $this->actingAs($user)->post(route('meeting-types.store'), ['name' => 'Test']);

        $response->assertForbidden();
    }

    public function test_store_creates_meeting_type(): void
    {
        $user = $this->adminUser('meeting-types');

        $this->actingAs($user)->post(route('meeting-types.store'), [
            'name' => 'New Type',
            'description' => 'A description',
        ]);

        $this->assertDatabaseHas('meeting_types', [
            'name' => 'New Type',
            'description' => 'A description',
        ]);
    }

    public function test_store_redirects_back_on_success(): void
    {
        $user = $this->adminUser('meeting-types');

        $response = $this->actingAs($user)->post(route('meeting-types.store'), [
            'name' => 'Another Type',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_store_validates_name_is_required(): void
    {
        $user = $this->adminUser('meeting-types');

        $response = $this->actingAs($user)->post(route('meeting-types.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('meeting_types', 0);
    }

    public function test_store_validates_name_is_unique(): void
    {
        $user = $this->adminUser('meeting-types');
        MeetingType::factory()->create(['name' => 'Duplicate']);

        $response = $this->actingAs($user)->post(route('meeting-types.store'), [
            'name' => 'Duplicate',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('meeting_types', 1);
    }

    public function test_store_allows_nullable_description(): void
    {
        $user = $this->adminUser('meeting-types');

        $this->actingAs($user)->post(route('meeting-types.store'), [
            'name' => 'No Desc Type',
            'description' => '',
        ]);

        $this->assertDatabaseHas('meeting_types', ['name' => 'No Desc Type']);
    }

    public function test_update_requires_authentication(): void
    {
        $type = MeetingType::factory()->create();

        $response = $this->put(route('meeting-types.update', $type), ['name' => 'Changed']);

        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('meeting-types');
        $type = MeetingType::factory()->create();

        $response = $this->actingAs($user)->put(route('meeting-types.update', $type), ['name' => 'Changed']);

        $response->assertForbidden();
    }

    public function test_update_modifies_existing_meeting_type(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create(['name' => 'Old Name', 'description' => 'Old desc']);

        $this->actingAs($user)->put(route('meeting-types.update', $type), [
            'name' => 'New Name',
            'description' => 'New desc',
        ]);

        $this->assertDatabaseHas('meeting_types', ['id' => $type->id, 'name' => 'New Name', 'description' => 'New desc']);
    }

    public function test_update_redirects_back_on_success(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();

        $response = $this->actingAs($user)->put(route('meeting-types.update', $type), [
            'name' => 'Updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_update_validates_name_is_required(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($user)->put(route('meeting-types.update', $type), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('meeting_types', ['id' => $type->id, 'name' => 'Original']);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create(['name' => 'Same Name']);

        $response = $this->actingAs($user)->put(route('meeting-types.update', $type), [
            'name' => 'Same Name',
            'description' => 'Updated description',
        ]);

        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('meeting_types', ['id' => $type->id, 'description' => 'Updated description']);
    }

    public function test_update_validates_name_unique_across_other_records(): void
    {
        $user = $this->adminUser('meeting-types');
        MeetingType::factory()->create(['name' => 'Taken']);
        $type = MeetingType::factory()->create(['name' => 'Mine']);

        $response = $this->actingAs($user)->put(route('meeting-types.update', $type), [
            'name' => 'Taken',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_destroy_requires_authentication(): void
    {
        $type = MeetingType::factory()->create();

        $response = $this->delete(route('meeting-types.destroy', $type));

        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('meeting-types');
        $type = MeetingType::factory()->create();

        $response = $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $response->assertForbidden();
    }

    public function test_destroy_deletes_meeting_type(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();

        $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $this->assertDatabaseMissing('meeting_types', ['id' => $type->id]);
    }

    public function test_destroy_redirects_back_on_success(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();

        $response = $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_destroy_also_deletes_associated_meetings_via_cascade(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();
        $pupil = Pupil::factory()->create(['onboarded_by' => $user->id]);

        $meeting = Meeting::factory()->create([
            'meeting_type_id' => $type->id,
            'pupil_id' => $pupil->id,
        ]);

        $response = $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('meeting_types', ['id' => $type->id]);
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }

    public function test_destroy_prevents_deletion_if_meetings_assigned(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();

        MeetingType::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception('fk', 23000));
        });

        $response = $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        MeetingType::flushEventListeners();
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();

        MeetingType::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('meeting-types.destroy', $type));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        MeetingType::flushEventListeners();
    }

    public function test_meeting_type_has_many_meetings(): void
    {
        $user = $this->adminUser('meeting-types');
        $type = MeetingType::factory()->create();
        $pupil = Pupil::factory()->create(['onboarded_by' => $user->id]);

        Meeting::factory()->count(2)->create([
            'meeting_type_id' => $type->id,
            'pupil_id' => $pupil->id,
        ]);

        $this->assertCount(2, $type->meetings);
    }
}
