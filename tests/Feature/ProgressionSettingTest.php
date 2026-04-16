<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;

class ProgressionSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('progression-settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('progression-settings.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['manage-school-progression-settings']);
        
        $response = $this->actingAs($user)->get(route('progression-settings.index'));

        $response->assertOk();
        $response->assertViewIs('progression_settings');
        $response->assertViewHas('settings');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->put(route('progression-settings.update'), ['progression_update_month' => '08']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->put(route('progression-settings.update'), ['progression_update_month' => '08']);
        $response->assertForbidden();
    }

    public function test_update_validates_required_fields_and_validates_date(): void
    {
        $user = $this->userWithPermissions(['manage-school-progression-settings']);

        $response = $this->actingAs($user)->put(route('progression-settings.update'), [
            'progression_update_month' => '02',
            'progression_update_day' => '30', // invalid as February doesn't have 30th
            'progression_min_year_group' => '1',
            'progression_max_year_group' => '13',
        ]);

        $response->assertSessionHasErrors('progression_update_day');
    }

    public function test_update_modifies_settings(): void
    {
        $user = $this->userWithPermissions(['manage-school-progression-settings']);

        $response = $this->actingAs($user)->put(route('progression-settings.update'), [
            'progression_update_month' => '08',
            'progression_update_day' => '31',
            'progression_min_year_group' => '7',
            'progression_max_year_group' => '11',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('08-31', Setting::get('progression_update_date'));
        $this->assertEquals('7', Setting::get('progression_min_year_group'));
        $this->assertEquals('11', Setting::get('progression_max_year_group'));
    }
}
