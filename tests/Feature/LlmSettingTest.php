<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;

class LlmSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('llm-settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('llm-settings.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['manage-llm-settings']);
        
        $response = $this->actingAs($user)->get(route('llm-settings.index'));

        $response->assertOk();
        $response->assertViewIs('llm_settings');
        $response->assertViewHas('llm_provider');
        $response->assertViewHas('llm_api_key');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->put(route('llm-settings.update'), ['llm_provider' => 'none']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('manage-settings'); // different permission
        $response = $this->actingAs($user)->put(route('llm-settings.update'), ['llm_provider' => 'none']);
        $response->assertForbidden();
    }

    public function test_update_validates_provider_and_api_key(): void
    {
        $user = $this->userWithPermissions(['manage-llm-settings']);

        $response = $this->actingAs($user)->put(route('llm-settings.update'), [
            'llm_provider' => 'openai',
            // missing api key
        ]);

        $response->assertSessionHasErrors('llm_api_key');
    }

    public function test_update_modifies_settings(): void
    {
        $user = $this->userWithPermissions(['manage-llm-settings']);

        $response = $this->actingAs($user)->put(route('llm-settings.update'), [
            'llm_provider' => 'mistral',
            'llm_api_key' => 'test_api_key',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('mistral', Setting::get('llm_provider'));
        $this->assertEquals('test_api_key', Setting::get('llm_api_key'));
    }
}
