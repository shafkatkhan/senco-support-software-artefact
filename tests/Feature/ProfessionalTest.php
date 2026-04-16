<?php

namespace Tests\Feature;

use App\Models\Diagnosis;
use App\Models\Professional;
use App\Models\Pupil;
use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfessionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('professionals.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('professionals.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('professionals');
        Professional::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('professionals.index'));

        $response->assertOk();
        $response->assertViewIs('professionals');
        $response->assertViewHas('professionals');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('professionals.store'), ['first_name' => 'Test', 'last_name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('professionals');
        $response = $this->actingAs($user)->post(route('professionals.store'), ['first_name' => 'Test', 'last_name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_professional(): void
    {
        $user = $this->adminUser('professionals');

        $this->actingAs($user)->post(route('professionals.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role' => 'Doctor',
        ]);

        $this->assertDatabaseHas('professionals', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role' => 'Doctor',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('professionals');

        $response = $this->actingAs($user)->post(route('professionals.store'), [
            'first_name' => '',
            'last_name' => '',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name']);
    }

    public function test_update_requires_authentication(): void
    {
        $professional = Professional::factory()->create();
        $response = $this->put(route('professionals.update', $professional), ['first_name' => 'Changed', 'last_name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('professionals');
        $professional = Professional::factory()->create();
        
        $response = $this->actingAs($user)->put(route('professionals.update', $professional), ['first_name' => 'Changed', 'last_name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_professional(): void
    {
        $user = $this->adminUser('professionals');
        $professional = Professional::factory()->create([
            'first_name' => 'Old', 
            'last_name' => 'Name',
        ]);

        $response = $this->actingAs($user)->put(route('professionals.update', $professional), [
            'first_name' => 'New',
            'last_name' => 'Name',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('professionals', [
            'id' => $professional->id,
            'first_name' => 'New',
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $professional = Professional::factory()->create();
        $response = $this->delete(route('professionals.destroy', $professional));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('professionals');
        $professional = Professional::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('professionals.destroy', $professional));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_professional(): void
    {
        $user = $this->adminUser('professionals');
        $professional = Professional::factory()->create();

        $response = $this->actingAs($user)->delete(route('professionals.destroy', $professional));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('professionals', ['id' => $professional->id]);
    }

    public function test_destroy_prevents_deletion_if_records_assigned(): void
    {
        $user = $this->adminUser('professionals');
        $professional = Professional::factory()->create();

        Professional::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception('fk', 23000));
        });

        $response = $this->actingAs($user)->delete(route('professionals.destroy', $professional));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Professional::flushEventListeners();
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('professionals');
        $professional = Professional::factory()->create();

        Professional::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('professionals.destroy', $professional));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        Professional::flushEventListeners();
    }

    public function test_professional_has_many_records_and_diagnoses(): void
    {
        $onboarder = $this->userWithPermissions([]);
        $pupil = Pupil::factory()->create(['onboarded_by' => $onboarder->id]);
        $professional = Professional::factory()->create();
        $record = Record::factory()->create([
            'pupil_id' => $pupil->id,
            'record_type_id' => RecordType::factory()->create()->id,
            'professional_id' => $professional->id,
        ]);
        $diagnosis = Diagnosis::factory()->create([
            'pupil_id' => $pupil->id,
            'professional_id' => $professional->id,
        ]);

        $this->assertTrue($professional->fresh()->records->first()->is($record));
        $this->assertTrue($professional->fresh()->diagnoses->first()->is($diagnosis));
    }
}
