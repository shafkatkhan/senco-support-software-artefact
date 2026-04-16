<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('backups.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('backups.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('download-backups'); // uses view-download-backups
        
        $response = $this->actingAs($user)->get(route('backups.index'));

        $response->assertOk();
        $response->assertViewIs('backups');
        $response->assertViewHas('backups');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('backups.store'));
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->post(route('backups.store'));
        $response->assertForbidden();
    }

    public function test_store_handles_backup_failure_gracefully(): void
    {
        $user = $this->adminUser('backups');
        Storage::fake('local');
        
        $response = $this->actingAs($user)->post(route('backups.store'));
        
        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('error');
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_store_creates_backup_successfully(): void
    {
        $user = $this->adminUser('backups');
        Storage::fake('local');
        
        // mock the mysqldump initialisation to avoid the database connection exception
        \Mockery::mock('overload:\Ifsnop\Mysqldump\Mysqldump')
            ->shouldReceive('start')
            ->andReturnNull();

        $response = $this->actingAs($user)->post(route('backups.store'));
        
        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('success');
    }

    public function test_download_returns_file_if_exists(): void
    {
        $user = $this->viewerUser('download-backups');
        $directory = env('APP_NAME', 'laravel-backup');
        
        Storage::fake('local');
        Storage::put("{$directory}/test.sql", 'database dump');
        
        $relative_path = "{$directory}/test.sql";
        $response = $this->actingAs($user)->get(route('backups.download', ['file_path' => urlencode($relative_path)]));
        
        $response->assertOk();
        $response->assertDownload('test.sql');
    }

    public function test_download_returns_error_if_missing(): void
    {
        $user = $this->viewerUser('download-backups');
        Storage::fake('local');
        
        $response = $this->actingAs($user)->get(route('backups.download', ['file_path' => 'missing.sql']));
        
        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('error');
    }

    public function test_destroy_deletes_backup_file(): void
    {
        $user = $this->adminUser('backups');
        $directory = env('APP_NAME', 'laravel-backup');
        
        Storage::fake('local');
        Storage::put("{$directory}/test.sql", 'database dump');
        
        $relative_path = "{$directory}/test.sql";
        $response = $this->actingAs($user)->delete(route('backups.destroy', ['file_path' => urlencode($relative_path)]));
        
        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('success');
        Storage::disk('local')->assertMissing($relative_path);
    }

    public function test_destroy_returns_error_if_missing(): void
    {
        $user = $this->adminUser('backups');
        Storage::fake('local');
        
        $response = $this->actingAs($user)->delete(route('backups.destroy', ['file_path' => 'missing.sql']));
        
        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('error');
    }
}
