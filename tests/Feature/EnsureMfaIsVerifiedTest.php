<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureMfaIsVerified;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EnsureMfaIsVerifiedTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_unauthenticated_request(): void
    {
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_allows_request_when_auth_check_fails(): void
    {
        Auth::shouldReceive('check')->andThrow(new \Exception('Database unavailable'));
        $request = Request::create('/pupils', 'GET');

        $response = $this->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_allows_authenticated_user_when_mfa_is_disabled(): void
    {
        $user = $this->createMfaUser();
        Setting::set('mfa_method', 'none');
        $request = $this->requestForUser($user, 'pupils.index');

        $response = $this->actingAs($user)->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_allows_excluded_routes_for_pending_user(): void
    {
        $user = $this->createMfaUser();
        Setting::set('mfa_method', 'email');
        $request = $this->requestForUser($user, 'mfa-setup.index');

        $response = $this->actingAs($user)->handleMiddleware($request);

        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_redirects_pending_setup_to_mfa_setup(): void
    {
        $user = $this->createMfaUser();
        Setting::set('mfa_method', 'email');
        $request = $this->requestForUser($user, 'pupils.index');

        $response = $this->actingAs($user)->handleMiddleware($request);

        $response->assertRedirect(route('mfa-setup.index'));
        $response->assertSessionHas('warning');
    }

    public function test_redirects_verified_user_to_challenge_when_session_is_unverified(): void
    {
        $user = $this->createMfaUser(['mfa_verified_at' => now()]);
        Setting::set('mfa_method', 'email');
        $request = $this->requestForUser($user, 'pupils.index');

        $response = $this->actingAs($user)->handleMiddleware($request);

        $response->assertRedirect(route('mfa-challenge.index'));
    }

    protected function handleMiddleware(Request $request): TestResponse
    {
        $response = (new EnsureMfaIsVerified())->handle($request, function () {
            return response('OK');
        });

        return TestResponse::fromBaseResponse($response);
    }

    protected function createMfaUser(array $attributes = []): User
    {
        $group = \App\Models\UserGroup::factory()->create();

        return User::factory()->create(array_merge([
            'user_group_id' => $group->id,
        ], $attributes));
    }

    protected function requestForUser(User $user, string $routeName): Request
    {
        $request = Request::create('/pupils', 'GET');
        $route = (new RoutingRoute(['GET'], '/pupils', []))->name($routeName);

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        return $request;
    }
}
