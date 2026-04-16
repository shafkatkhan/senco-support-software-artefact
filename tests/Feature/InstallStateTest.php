<?php

namespace Tests\Feature;

use App\Support\InstallState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstallStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        InstallState::reset();
    }

    protected function tearDown(): void
    {
        InstallState::reset();

        parent::tearDown();
    }

    public function test_is_installed_is_false_by_default(): void
    {
        $this->assertFalse(InstallState::isInstalled());
    }

    public function test_mark_installed_sets_system_as_installed(): void
    {
        InstallState::markInstalled();

        $this->assertTrue(InstallState::isInstalled());
    }

    public function test_clear_installed_removes_installed_state(): void
    {
        InstallState::markInstalled();

        InstallState::clearInstalled();

        $this->assertFalse(InstallState::isInstalled());
    }

    public function test_is_installed_resets_state_when_users_table_is_missing(): void
    {
        InstallState::markInstalled();
        InstallState::markLanguageSetupPending();
        InstallState::put('auto_translations', ['Hello' => 'Bonjour']);
        InstallState::put('locale_name', 'French');
        Schema::shouldReceive('hasTable')->with('users')->andReturn(false);

        $this->assertFalse(InstallState::isInstalled());
        $this->assertNull(InstallState::get('locale_name'));
        $this->assertNull(InstallState::get('auto_translations'));
    }

    public function test_is_installed_returns_false_when_schema_check_fails(): void
    {
        InstallState::markInstalled();
        Schema::shouldReceive('hasTable')->with('users')->andThrow(new \Exception('Database unavailable'));

        $this->assertFalse(InstallState::isInstalled());
    }

    public function test_language_setup_pending_is_false_when_users_table_is_missing(): void
    {
        InstallState::markLanguageSetupPending();
        Schema::shouldReceive('hasTable')->with('users')->andReturn(false);

        $this->assertFalse(InstallState::isLanguageSetupPending());
    }

    public function test_mark_and_clear_language_setup_pending(): void
    {
        InstallState::markLanguageSetupPending();

        $this->assertTrue(InstallState::isLanguageSetupPending());

        InstallState::clearLanguageSetupPending();

        $this->assertFalse(InstallState::isLanguageSetupPending());
    }

    public function test_put_and_get_store_install_values(): void
    {
        InstallState::put('locale_name', 'French');

        $this->assertEquals('French', InstallState::get('locale_name'));
        $this->assertEquals('fallback', InstallState::get('missing_key', 'fallback'));
    }

    public function test_reset_clears_installation_values(): void
    {
        InstallState::markInstalled();
        InstallState::markLanguageSetupPending();
        InstallState::put('auto_translations', ['Hello' => 'Bonjour']);
        InstallState::put('locale_name', 'French');

        InstallState::reset();

        $this->assertFalse(InstallState::isInstalled());
        $this->assertFalse(InstallState::isLanguageSetupPending());
        $this->assertNull(InstallState::get('auto_translations'));
        $this->assertNull(InstallState::get('locale_name'));
    }
}
