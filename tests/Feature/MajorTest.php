<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MajorTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('majors.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('majors.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('majors');
        Major::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('majors.index'));

        $response->assertOk();
        $response->assertViewIs('majors');
        $response->assertViewHas('majors');
        $response->assertViewHas('subjects');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('majors.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('majors');
        $response = $this->actingAs($user)->post(route('majors.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_major_without_relations(): void
    {
        $user = $this->adminUser('majors');

        $this->actingAs($user)->post(route('majors.store'), [
            'name' => 'Test Major',
            'code' => 'TM1',
        ]);

        $this->assertDatabaseHas('majors', [
            'name' => 'Test Major',
            'code' => 'TM1',
        ]);
    }

    public function test_store_creates_major_with_relations(): void
    {
        $user = $this->adminUser('majors');
        $subject = Subject::factory()->create();

        $response = $this->actingAs($user)->post(route('majors.store'), [
            'name' => 'Science',
            'code' => 'SCI101',
            'subject_ids' => [$subject->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $major = Major::where('name', 'Science')->first();
        $this->assertNotNull($major);
        $this->assertCount(1, $major->subjects);
        $this->assertEquals($subject->id, $major->subjects->first()->id);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('majors');

        $response = $this->actingAs($user)->post(route('majors.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_requires_authentication(): void
    {
        $major = Major::factory()->create();
        $response = $this->put(route('majors.update', $major), ['name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('majors');
        $major = Major::factory()->create();
        
        $response = $this->actingAs($user)->put(route('majors.update', $major), ['name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_major_and_syncs_relations(): void
    {
        $user = $this->adminUser('majors');
        $major = Major::factory()->create(['name' => 'Old Name', 'code' => 'OLD']);
        $oldSub = Subject::factory()->create();
        $newSub = Subject::factory()->create();
        
        $major->subjects()->attach($oldSub->id);

        $response = $this->actingAs($user)->put(route('majors.update', $major), [
            'name' => 'New Name',
            'code' => 'NEW',
            'subject_ids' => [$newSub->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('majors', [
            'id' => $major->id,
            'name' => 'New Name',
            'code' => 'NEW',
        ]);

        $this->assertCount(1, $major->fresh()->subjects);
        $this->assertEquals($newSub->id, $major->fresh()->subjects->first()->id);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('majors');
        $major = Major::factory()->create(['name' => 'Same Name', 'code' => 'CODE1']);

        $response = $this->actingAs($user)->put(route('majors.update', $major), [
            'name' => 'Same Name',
            'code' => 'CODE1',
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_destroy_requires_authentication(): void
    {
        $major = Major::factory()->create();
        $response = $this->delete(route('majors.destroy', $major));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('majors');
        $major = Major::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('majors.destroy', $major));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_major(): void
    {
        $user = $this->adminUser('majors');
        $major = Major::factory()->create();

        $response = $this->actingAs($user)->delete(route('majors.destroy', $major));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('majors', ['id' => $major->id]);
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('majors');
        $major = Major::factory()->create();

        Major::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('majors.destroy', $major));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Major::flushEventListeners();
    }
}
