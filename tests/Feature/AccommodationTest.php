<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Diet;
use App\Models\Pupil;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccommodationTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('accommodations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('accommodations.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('accommodations');
        Accommodation::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('accommodations.index'));

        $response->assertOk();
        $response->assertViewIs('accommodations');
        $response->assertViewHas('accommodations');
        $response->assertViewHas('title');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('accommodations.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('accommodations');
        $response = $this->actingAs($user)->post(route('accommodations.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_accommodation(): void
    {
        $user = $this->adminUser('accommodations');

        $response = $this->actingAs($user)->post(route('accommodations.store'), [
            'name' => 'Extra Time',
            'description' => 'Allow extra time during assessments.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accommodations', [
            'name' => 'Extra Time',
            'description' => 'Allow extra time during assessments.',
        ]);
    }

    public function test_store_validates_required_and_unique_name(): void
    {
        $user = $this->adminUser('accommodations');
        Accommodation::factory()->create(['name' => 'Reader']);

        $response = $this->actingAs($user)->post(route('accommodations.store'), [
            'name' => 'Reader',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_requires_authentication(): void
    {
        $accommodation = Accommodation::factory()->create();
        $response = $this->put(route('accommodations.update', $accommodation), ['name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('accommodations');
        $accommodation = Accommodation::factory()->create();

        $response = $this->actingAs($user)->put(route('accommodations.update', $accommodation), ['name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_accommodation(): void
    {
        $user = $this->adminUser('accommodations');
        $accommodation = Accommodation::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->put(route('accommodations.update', $accommodation), [
            'name' => 'New Name',
            'description' => 'Updated description.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accommodations', [
            'id' => $accommodation->id,
            'name' => 'New Name',
            'description' => 'Updated description.',
        ]);
    }

    public function test_update_allows_keeping_same_name(): void
    {
        $user = $this->adminUser('accommodations');
        $accommodation = Accommodation::factory()->create(['name' => 'Same Name']);

        $response = $this->actingAs($user)->put(route('accommodations.update', $accommodation), [
            'name' => 'Same Name',
            'description' => 'Same description.',
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_destroy_requires_authentication(): void
    {
        $accommodation = Accommodation::factory()->create();
        $response = $this->delete(route('accommodations.destroy', $accommodation));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('accommodations');
        $accommodation = Accommodation::factory()->create();

        $response = $this->actingAs($user)->delete(route('accommodations.destroy', $accommodation));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_accommodation(): void
    {
        $user = $this->adminUser('accommodations');
        $accommodation = Accommodation::factory()->create();

        $response = $this->actingAs($user)->delete(route('accommodations.destroy', $accommodation));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('accommodations', ['id' => $accommodation->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('accommodations');
        $accommodation = Accommodation::factory()->create();

        Accommodation::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('accommodations.destroy', $accommodation));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        Accommodation::flushEventListeners();
    }

    public function test_accommodation_has_many_subjects(): void
    {
        $accommodation = Accommodation::factory()->create();
        $subject = Subject::factory()->create();

        $accommodation->subjects()->attach($subject->id);

        $this->assertCount(1, $accommodation->fresh()->subjects);
        $this->assertTrue($accommodation->fresh()->subjects->first()->is($subject));
    }

    public function test_accommodation_has_many_diets_with_pivot_details(): void
    {
        $accommodation = Accommodation::factory()->create();
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create(['onboarded_by' => $onboarder->id]);
        $diet = Diet::factory()->create(['pupil_id' => $pupil->id]);

        $accommodation->diets()->attach($diet->id, [
            'status' => 'Approved',
            'details' => 'Use for written assessments.',
        ]);

        $relatedDiet = $accommodation->fresh()->diets->first();
        $this->assertTrue($relatedDiet->is($diet));
        $this->assertEquals('Approved', $relatedDiet->pivot->status);
        $this->assertEquals('Use for written assessments.', $relatedDiet->pivot->details);
    }
}
