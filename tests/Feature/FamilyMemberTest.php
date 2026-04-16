<?php

namespace Tests\Feature;

use App\Models\FamilyMember;
use App\Models\Pupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FamilyMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('family-members.store'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('family-members');
        $response = $this->actingAs($user)->post(route('family-members.store'), []);
        $response->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('family-members');

        $response = $this->actingAs($user)->post(route('family-members.store'), [
            'first_name' => '',
        ]);

        $response->assertSessionHasErrors(['pupil_id', 'first_name', 'last_name']);
    }

    public function test_extract_from_file_validates_request(): void
    {
        $user = $this->adminUser('family-members');

        $response = $this->actingAs($user)->post(route('family-members.extract-file'), []);

        $response->assertSessionHasErrors('file');
    }

    public function test_store_creates_family_member_and_attachments(): void
    {
        $user = $this->adminUser('family-members');
        $pupil = Pupil::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $response = $this->actingAs($user)->post(route('family-members.store'), [
            'pupil_id' => $pupil->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'relationship' => 'Mother',
            'is_primary_contact' => 1,
            'additional_attachments' => [$file],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('family_members', [
            'pupil_id' => $pupil->id,
            'first_name' => 'Jane',
        ]);
        
        $member = FamilyMember::where('first_name', 'Jane')->first();
        $this->assertCount(1, $member->attachments);
    }

    public function test_update_modifies_family_member(): void
    {
        $user = $this->adminUser('family-members');
        $member = FamilyMember::factory()->create();

        $response = $this->actingAs($user)->put(route('family-members.update', $member), [
            'first_name' => 'New Name',
            'last_name' => 'Same',
            'relationship' => 'Father',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('family_members', [
            'id' => $member->id,
            'first_name' => 'New Name',
        ]);
    }

    public function test_destroy_deletes_family_member(): void
    {
        $user = $this->adminUser('family-members');
        $member = FamilyMember::factory()->create();

        $response = $this->actingAs($user)->delete(route('family-members.destroy', $member));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('family_members', ['id' => $member->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('family-members');
        $member = FamilyMember::factory()->create();

        FamilyMember::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('family-members.destroy', $member));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        FamilyMember::flushEventListeners();
    }
}
