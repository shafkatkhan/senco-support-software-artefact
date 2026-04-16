<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSystemInstalled;
use App\Support\InstallState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EnsureSystemInstalledTest extends TestCase
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

    public function test_install_routes_skip_install_check(): void
    {
        $request = Request::create('/install/process', 'POST');

        $response = $this->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_language_setup_pending_redirects_to_language_setup(): void
    {
        InstallState::markLanguageSetupPending();
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertRedirect(route('install.lang_setup_view'));
    }

    public function test_installed_system_allows_request(): void
    {
        InstallState::markInstalled();
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_existing_users_table_marks_system_as_installed_and_allows_request(): void
    {
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
        $this->assertTrue(InstallState::isInstalled());
    }

    public function test_uninstalled_system_redirects_to_install_when_users_table_is_missing(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andReturn(false);
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertRedirect(route('install.index'));
    }

    public function test_install_check_failure_redirects_to_install(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andThrow(new \Exception('Database unavailable'));
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertRedirect(route('install.index'));
    }

    protected function handleMiddleware(Request $request): TestResponse
    {
        $response = (new EnsureSystemInstalled())->handle($request, function () {
            return response('OK');
        });

        return TestResponse::fromBaseResponse($response);
    }
}
