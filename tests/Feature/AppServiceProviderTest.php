<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_boot_sets_language_direction_from_settings(): void
    {
        Setting::set('app_language_direction', 'rtl');
        config(['app.language_direction' => 'ltr']);

        $this->bootProvider();

        $this->assertEquals('rtl', config('app.language_direction'));
    }

    public function test_boot_sets_mail_config_from_settings_when_mail_host_exists(): void
    {
        Setting::set('mail_host', 'smtp.example.com');
        Setting::set('mail_port', '587');
        Setting::set('mail_username', 'user');
        Setting::set('mail_password', 'password');
        Setting::set('mail_encryption', 'tls');
        Setting::set('mail_from_address', 'noreply@example.com');
        Setting::set('mail_from_name', 'Support');

        $this->bootProvider();

        $this->assertEquals('smtp', config('mail.default'));
        $this->assertEquals('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertEquals('587', config('mail.mailers.smtp.port'));
        $this->assertEquals('user', config('mail.mailers.smtp.username'));
        $this->assertEquals('password', config('mail.mailers.smtp.password'));
        $this->assertEquals('tls', config('mail.mailers.smtp.encryption'));
        $this->assertEquals('noreply@example.com', config('mail.from.address'));
        $this->assertEquals('Support', config('mail.from.name'));
    }

    public function test_boot_ignores_database_errors(): void
    {
        config(['mail.default' => 'array']);
        Schema::shouldReceive('hasTable')->with('settings')->andThrow(new \Exception('Database unavailable'));

        $this->bootProvider();

        $this->assertEquals('array', config('mail.default'));
    }

    public function test_gate_before_allows_user_with_permission(): void
    {
        $user = $this->userWithPermissions(['manage-test-area']);

        $this->assertTrue(Gate::forUser($user)->allows('manage-test-area'));
    }

    protected function bootProvider(): void
    {
        (new AppServiceProvider($this->app))->boot();
    }
}
