<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\InstallState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class InstallControllerTest extends TestCase
{
    use RefreshDatabase;

    protected array $temporaryDatabases = [];
    protected string|false $originalAppLocale;
    protected ?string $originalEnvContents = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalAppLocale = getenv('APP_LOCALE');
        InstallState::reset();
    }

    protected function tearDown(): void
    {
        InstallState::reset();
        config(['database.default' => 'sqlite']);
        $this->restoreAppLocale();
        $this->restoreEnvFile();

        foreach ($this->temporaryDatabases as $database) {
            if (file_exists($database)) {
                unlink($database);
            }
        }

        parent::tearDown();
    }

    public function test_index_returns_install_view_when_system_is_not_installed(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andReturn(false);

        $response = $this->get(route('install.index'));

        $response->assertOk();
        $response->assertViewIs('install');
    }

    public function test_index_redirects_to_login_when_system_is_installed(): void
    {
        InstallState::markInstalled();

        $response = $this->get(route('install.index'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_index_redirects_to_language_setup_when_language_setup_is_pending(): void
    {
        InstallState::markLanguageSetupPending();

        $response = $this->get(route('install.index'));

        $response->assertRedirect(route('install.lang_setup_view'));
    }

    public function test_index_marks_installed_if_users_table_exists(): void
    {
        $response = $this->get(route('install.index'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertTrue(InstallState::isInstalled());
    }

    public function test_index_returns_install_view_when_schema_check_fails(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andThrow(new \Exception('Database unavailable'));

        $response = $this->get(route('install.index'));

        $response->assertOk();
        $response->assertViewIs('install');
    }

    public function test_process_validates_required_fields(): void
    {
        $response = $this->post(route('install.process'), []);

        $response->assertSessionHasErrors([
            'app_url',
            'app_locale',
            'db_host',
            'db_port',
            'db_name',
            'db_username',
            'llm_provider',
        ]);
    }

    public function test_process_installs_english_locale_and_redirects_to_login(): void
    {
        $database = $this->createMysqlSettingsDatabase();
        $this->mockSuccessfulDatabaseInstall();
        File::shouldReceive('exists')->once()->with(base_path('.env'))->andReturn(false);

        $response = $this->post(route('install.process'), $this->installPayload([
            'app_locale' => 'en-GB',
            'db_name' => $database,
            'llm_provider' => 'none',
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(InstallState::isInstalled());
        $this->assertFalse(InstallState::isLanguageSetupPending());
        $this->assertEquals('none', Setting::get('llm_provider'));
        $this->assertNull(Setting::get('llm_api_key'));
    }

    public function test_process_installs_non_english_locale_and_redirects_to_language_setup(): void
    {
        $database = $this->createMysqlSettingsDatabase();
        $this->mockSuccessfulDatabaseInstall();
        File::shouldReceive('exists')->once()->with(base_path('.env'))->andReturn(false);

        $response = $this->post(route('install.process'), $this->installPayload([
            'app_locale' => 'fr',
            'db_name' => $database,
            'llm_provider' => 'none',
        ]));

        $response->assertRedirect(route('install.lang_setup_view'));
        $response->assertSessionHas('success');

        $this->assertFalse(InstallState::isInstalled());
        $this->assertTrue(InstallState::isLanguageSetupPending());
        $this->assertEquals('French (Français)', InstallState::get('locale_name'));
        $this->assertEquals([], InstallState::get('auto_translations'));
    }

    public function test_process_restores_from_uploaded_backup(): void
    {
        $database = $this->createMysqlSettingsDatabase();
        Artisan::shouldReceive('call')->once()->with('db:wipe', ['--force' => true])->andReturn(0);
        File::shouldReceive('exists')->once()->with(base_path('.env'))->andReturn(false);

        $response = $this->post(route('install.process'), $this->installPayload([
            'db_name' => $database,
            'restore_file' => UploadedFile::fake()->createWithContent('backup.sql', 'select 1;'),
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertTrue(InstallState::isInstalled());
    }

    public function test_process_updates_env_file_when_it_exists(): void
    {
        $database = $this->createMysqlSettingsDatabase();
        $this->backupEnvFile();
        file_put_contents(base_path('.env'), "APP_URL=\"http://old.test\"\nDB_HOST=\"old-host\"\n");
        $this->mockSuccessfulDatabaseInstall();

        $response = $this->post(route('install.process'), $this->installPayload([
            'app_url' => 'http://new.test',
            'app_locale' => 'en',
            'db_name' => $database,
            'db_host' => 'database.test',
            'db_password' => 'secret"value',
        ]));

        $response->assertRedirect(route('login'));

        $env = file_get_contents(base_path('.env'));
        $this->assertStringContainsString('APP_URL="http://new.test"', $env);
        $this->assertStringContainsString('DB_HOST="database.test"', $env);
        $this->assertStringContainsString('DB_PASSWORD="secret\"value"', $env);
        $this->assertStringContainsString('APP_LANGUAGE_DIRECTION="ltr"', $env);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_process_uses_gemini_for_auto_translations_and_handles_empty_auto_translation(): void
    {
        $database = $this->createMysqlSettingsDatabase();
        $this->mockSuccessfulDatabaseInstall();
        File::shouldReceive('exists')->once()->with(base_path('.env'))->andReturn(false);

        $llm = \Mockery::mock('alias:App\Services\LlmService');
        $llm->shouldReceive('processRequest')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('string'), null, null, 'gemini', 'gemini-2.5-flash-lite', 'gemini-key')
            ->andReturn(['Dashboard' => 'Tableau de bord']);
        $llm->shouldReceive('processRequest')
            ->once()
            ->with(\Mockery::type('string'), \Mockery::type('string'))
            ->andReturn([]);

        $response = $this->post(route('install.process'), $this->installPayload([
            'app_locale' => 'fr',
            'db_name' => $database,
            'llm_provider' => 'gemini',
            'llm_api_key' => 'gemini-key',
        ]));

        $response->assertRedirect(route('install.lang_setup_view'));
        $this->assertEquals(['Dashboard' => 'Tableau de bord'], InstallState::get('auto_translations'));

        InstallState::reset();
        $database = $this->createMysqlSettingsDatabase();
        $this->mockSuccessfulDatabaseInstall();
        Artisan::shouldReceive('call')->once()->with('db:wipe', ['--force' => true])->andReturn(0);

        $response = $this->from(route('install.index'))->post(route('install.process'), $this->installPayload([
            'app_locale' => 'fr',
            'db_name' => $database,
            'llm_provider' => 'openai',
            'llm_api_key' => 'openai-key',
        ]));

        $response->assertRedirect(route('install.index'));
        $response->assertSessionHas('error');
    }

    public function test_process_cleans_up_and_redirects_back_when_installation_fails(): void
    {
        DB::shouldReceive('purge')->once()->with('mysql');
        DB::shouldReceive('reconnect')->once()->with('mysql');
        DB::shouldReceive('connection')->once()->andThrow(new \Exception('Connection failed'));
        Artisan::shouldReceive('call')->once()->with('db:wipe', ['--force' => true])->andReturn(0);

        $response = $this->from(route('install.index'))->post(route('install.process'), $this->installPayload());

        $response->assertRedirect(route('install.index'));
        $response->assertSessionHas('error', 'Connection failed');
        $this->assertFalse(InstallState::isInstalled());
    }

    public function test_process_ignores_cleanup_failure_when_installation_fails(): void
    {
        DB::shouldReceive('purge')->once()->with('mysql');
        DB::shouldReceive('reconnect')->once()->with('mysql');
        DB::shouldReceive('connection')->once()->andThrow(new \Exception('Connection failed'));
        Artisan::shouldReceive('call')->once()->with('db:wipe', ['--force' => true])->andThrow(new \Exception('Cleanup failed'));

        $response = $this->from(route('install.index'))->post(route('install.process'), $this->installPayload());

        $response->assertRedirect(route('install.index'));
        $response->assertSessionHas('error', 'Connection failed');
    }

    public function test_language_setup_view_redirects_to_install_when_not_pending(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andReturn(false);

        $response = $this->get(route('install.lang_setup_view'));

        $response->assertRedirect(route('install.index'));
    }

    public function test_language_setup_view_redirects_to_install_when_state_check_fails(): void
    {
        Schema::shouldReceive('hasTable')->with('users')->andThrow(new \Exception('Database unavailable'));

        $response = $this->get(route('install.lang_setup_view'));

        $response->assertRedirect(route('install.index'));
    }

    public function test_language_setup_view_redirects_to_login_when_already_installed(): void
    {
        InstallState::markInstalled();

        $response = $this->get(route('install.lang_setup_view'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_language_setup_view_returns_view_when_language_setup_is_pending(): void
    {
        InstallState::markLanguageSetupPending();
        InstallState::put('locale_name', 'Arabic');
        InstallState::put('auto_translations', ['Dashboard' => 'Translated dashboard']);
        $_ENV['APP_LOCALE'] = 'ar';
        $_SERVER['APP_LOCALE'] = 'ar';
        putenv('APP_LOCALE=ar');

        $response = $this->get(route('install.lang_setup_view'));

        $response->assertOk();
        $response->assertViewIs('lang_setup');
        $response->assertViewHas('locale_name', 'Arabic');
        $response->assertViewHas('app_direction', 'rtl');
        $response->assertViewHas('auto_translations', ['Dashboard' => 'Translated dashboard']);
    }

    public function test_language_setup_validates_required_fields(): void
    {
        $response = $this->post(route('install.lang_setup'), []);

        $response->assertSessionHasErrors(['app_locale', 'app_direction', 'translations']);
    }

    public function test_language_setup_saves_translations_and_marks_system_installed(): void
    {
        InstallState::markLanguageSetupPending();
        File::shouldReceive('put')
            ->once()
            ->with(base_path('lang/fr.json'), json_encode(['Hello' => 'Bonjour'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
            ->andReturn(23);

        $response = $this->post(route('install.lang_setup'), [
            'app_locale' => 'fr',
            'app_direction' => 'ltr',
            'translations' => [
                base64_encode('Hello') => 'Bonjour',
            ],
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(InstallState::isInstalled());
        $this->assertFalse(InstallState::isLanguageSetupPending());
        $this->assertEquals('ltr', Setting::get('app_language_direction'));
        $this->assertEquals('ltr', config('app.language_direction'));
    }

    public function test_language_setup_redirects_back_when_saving_translations_fails(): void
    {
        InstallState::markLanguageSetupPending();
        File::shouldReceive('put')->once()->andThrow(new \Exception('Unable to write file'));

        $response = $this->from(route('install.lang_setup_view'))->post(route('install.lang_setup'), [
            'app_locale' => 'fr',
            'app_direction' => 'ltr',
            'translations' => [
                base64_encode('Hello') => 'Bonjour',
            ],
        ]);

        $response->assertRedirect(route('install.lang_setup_view'));
        $response->assertSessionHas('error', 'Language setup failed: Unable to write file');
        $this->assertFalse(InstallState::isInstalled());
    }

    protected function installPayload(array $overrides = []): array
    {
        return array_merge([
            'app_url' => 'http://localhost',
            'app_locale' => 'en',
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_name' => 'senco',
            'db_username' => 'root',
            'db_password' => null,
            'llm_provider' => 'none',
        ], $overrides);
    }

    protected function restoreAppLocale(): void
    {
        if ($this->originalAppLocale === false) {
            unset($_ENV['APP_LOCALE'], $_SERVER['APP_LOCALE']);
            putenv('APP_LOCALE');

            return;
        }

        $_ENV['APP_LOCALE'] = $this->originalAppLocale;
        $_SERVER['APP_LOCALE'] = $this->originalAppLocale;
        putenv('APP_LOCALE=' . $this->originalAppLocale);
    }

    protected function backupEnvFile(): void
    {
        $this->originalEnvContents = file_exists(base_path('.env'))
            ? file_get_contents(base_path('.env'))
            : '';
    }

    protected function restoreEnvFile(): void
    {
        if ($this->originalEnvContents === null) {
            return;
        }

        file_put_contents(base_path('.env'), $this->originalEnvContents);
    }

    protected function mockSuccessfulDatabaseInstall(): void
    {
        Artisan::shouldReceive('call')->once()->with('migrate:fresh', ['--seed' => true])->andReturn(0);
    }

    protected function createMysqlSettingsDatabase(): string
    {
        $database = tempnam(sys_get_temp_dir(), 'senco_install_test_');
        $this->temporaryDatabases[] = $database;

        config([
            'database.connections.mysql.driver' => 'sqlite',
            'database.connections.mysql.database' => $database,
            'database.connections.mysql.prefix' => '',
            'database.connections.mysql.foreign_key_constraints' => true,
        ]);

        DB::purge('mysql');

        Schema::connection('mysql')->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::connection('mysql')->create('users', function (Blueprint $table) {
            $table->id();
        });

        return $database;
    }
}
