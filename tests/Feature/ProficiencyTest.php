<?php

namespace Tests\Feature;

use App\Models\Proficiency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProficiencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('proficiencies.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('proficiencies.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('proficiencies');
        Proficiency::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('proficiencies.index'));

        $response->assertOk();
        $response->assertViewIs('proficiencies');
        $response->assertViewHas('proficiencies');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('proficiencies.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('proficiencies');
        $response = $this->actingAs($user)->post(route('proficiencies.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_proficiency(): void
    {
        $user = $this->adminUser('proficiencies');

        $this->actingAs($user)->post(route('proficiencies.store'), [
            'name' => 'Advanced Reading',
            'description' => 'A description',
        ]);

        $this->assertDatabaseHas('proficiencies', [
            'name' => 'Advanced Reading',
            'description' => 'A description',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('proficiencies');

        $response = $this->actingAs($user)->post(route('proficiencies.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_requires_authentication(): void
    {
        $proficiency = Proficiency::factory()->create();
        $response = $this->put(route('proficiencies.update', $proficiency), ['name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('proficiencies');
        $proficiency = Proficiency::factory()->create();
        
        $response = $this->actingAs($user)->put(route('proficiencies.update', $proficiency), ['name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_proficiency(): void
    {
        $user = $this->adminUser('proficiencies');
        $proficiency = Proficiency::factory()->create(['name' => 'Old', 'description' => 'Desc']);

        $response = $this->actingAs($user)->put(route('proficiencies.update', $proficiency), [
            'name' => 'New',
            'description' => 'New Desc',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('proficiencies', [
            'id' => $proficiency->id,
            'name' => 'New',
            'description' => 'New Desc',
        ]);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('proficiencies');
        $proficiency = Proficiency::factory()->create(['name' => 'Same']);

        $response = $this->actingAs($user)->put(route('proficiencies.update', $proficiency), [
            'name' => 'Same',
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_destroy_requires_authentication(): void
    {
        $proficiency = Proficiency::factory()->create();
        $response = $this->delete(route('proficiencies.destroy', $proficiency));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('proficiencies');
        $proficiency = Proficiency::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('proficiencies.destroy', $proficiency));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_proficiency(): void
    {
        $user = $this->adminUser('proficiencies');
        $proficiency = Proficiency::factory()->create();

        $response = $this->actingAs($user)->delete(route('proficiencies.destroy', $proficiency));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('proficiencies', ['id' => $proficiency->id]);
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('proficiencies');
        $proficiency = Proficiency::factory()->create();

        Proficiency::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('proficiencies.destroy', $proficiency));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Proficiency::flushEventListeners();
    }
}
