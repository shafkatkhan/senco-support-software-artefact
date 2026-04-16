<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Proficiency;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('subjects.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('subjects.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('subjects');
        Subject::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('subjects.index'));

        $response->assertOk();
        $response->assertViewIs('subjects');
        $response->assertViewHas('subjects');
        $response->assertViewHas('accommodations');
        $response->assertViewHas('proficiencies');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('subjects.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('subjects');
        $response = $this->actingAs($user)->post(route('subjects.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_subject_without_relations(): void
    {
        $user = $this->adminUser('subjects');

        $this->actingAs($user)->post(route('subjects.store'), [
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $this->assertDatabaseHas('subjects', [
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);
    }

    public function test_store_creates_subject_with_relations(): void
    {
        $user = $this->adminUser('subjects');
        $accommodation = Accommodation::factory()->create();
        $proficiency = Proficiency::factory()->create();

        $response = $this->actingAs($user)->post(route('subjects.store'), [
            'name' => 'Science',
            'code' => 'SCI101',
            'accommodation_ids' => [$accommodation->id],
            'proficiency_ids' => [$proficiency->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $subject = Subject::where('name', 'Science')->first();
        $this->assertNotNull($subject);
        $this->assertCount(1, $subject->accommodations);
        $this->assertCount(1, $subject->proficiencies);
        $this->assertEquals($accommodation->id, $subject->accommodations->first()->id);
        $this->assertEquals($proficiency->id, $subject->proficiencies->first()->id);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('subjects');

        $response = $this->actingAs($user)->post(route('subjects.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_requires_authentication(): void
    {
        $subject = Subject::factory()->create();
        $response = $this->put(route('subjects.update', $subject), ['name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('subjects');
        $subject = Subject::factory()->create();
        
        $response = $this->actingAs($user)->put(route('subjects.update', $subject), ['name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_subject_and_syncs_relations(): void
    {
        $user = $this->adminUser('subjects');
        $subject = Subject::factory()->create(['name' => 'Old Name', 'code' => 'OLD']);
        $oldAcc = Accommodation::factory()->create();
        $newAcc = Accommodation::factory()->create();
        
        $subject->accommodations()->attach($oldAcc->id);

        $response = $this->actingAs($user)->put(route('subjects.update', $subject), [
            'name' => 'New Name',
            'code' => 'NEW',
            'accommodation_ids' => [$newAcc->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'New Name',
            'code' => 'NEW',
        ]);

        $this->assertCount(1, $subject->fresh()->accommodations);
        $this->assertEquals($newAcc->id, $subject->fresh()->accommodations->first()->id);
        $this->assertCount(0, $subject->fresh()->proficiencies);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('subjects');
        $subject = Subject::factory()->create(['name' => 'Same Name', 'code' => 'CODE1']);

        $response = $this->actingAs($user)->put(route('subjects.update', $subject), [
            'name' => 'Same Name',
            'code' => 'CODE1',
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_destroy_requires_authentication(): void
    {
        $subject = Subject::factory()->create();
        $response = $this->delete(route('subjects.destroy', $subject));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('subjects');
        $subject = Subject::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('subjects.destroy', $subject));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_subject(): void
    {
        $user = $this->adminUser('subjects');
        $subject = Subject::factory()->create();

        $response = $this->actingAs($user)->delete(route('subjects.destroy', $subject));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('subjects');
        $subject = Subject::factory()->create();

        Subject::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('subjects.destroy', $subject));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Subject::flushEventListeners();
    }
}
