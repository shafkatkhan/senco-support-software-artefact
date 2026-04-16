<?php

namespace Tests\Feature;

use App\Models\Diet;
use App\Models\Pupil;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DietTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('diets.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('add-to-diets');
        $response = $this->actingAs($user)->post(route('diets.store'), []);
        $response->assertForbidden();
    }

    public function test_store_creates_diet(): void
    {
        $user = $this->userWithPermissions(['add-to-diets', 'edit-diets', 'delete-diets']);
        $pupil = Pupil::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs($user)->post(route('diets.store'), [
            'pupil_id' => $pupil->id,
            'subject_id' => $subject->id,
            'accommodations' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('diets', [
            'pupil_id' => $pupil->id,
            'subject_id' => $subject->id,
        ]);
    }

    public function test_store_validates_unique_subject(): void
    {
        $user = $this->userWithPermissions(['add-to-diets', 'edit-diets', 'delete-diets']);
        $diet = Diet::factory()->create();

        $response = $this->actingAs($user)->post(route('diets.store'), [
            'pupil_id' => $diet->pupil_id,
            'subject_id' => $diet->subject_id,
        ]);

        $response->assertSessionHasErrors('subject_id');
    }

    public function test_update_modifies_diet(): void
    {
        $user = $this->userWithPermissions(['add-to-diets', 'edit-diets', 'delete-diets']);
        $diet = Diet::factory()->create();
        $newSubject = Subject::factory()->create();

        $response = $this->actingAs($user)->put(route('diets.update', $diet), [
            'subject_id' => $newSubject->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('diets', [
            'id' => $diet->id,
            'subject_id' => $newSubject->id,
        ]);
    }

    public function test_destroy_deletes_diet(): void
    {
        $user = $this->userWithPermissions(['add-to-diets', 'edit-diets', 'delete-diets']);
        $diet = Diet::factory()->create();

        $response = $this->actingAs($user)->delete(route('diets.destroy', $diet));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('diets', ['id' => $diet->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->userWithPermissions(['add-to-diets', 'edit-diets', 'delete-diets']);
        $diet = Diet::factory()->create();

        Diet::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('diets.destroy', $diet));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Diet::flushEventListeners();
    }
}
