<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');
        $appName = env('APP_NAME', 'laravel-backup');
        
        // backups stored in: storage/app/private/{$appName}
        $directory = rtrim($appName, '/');        
        $files = $disk->allFiles($directory);

        $backups = [];
        foreach ($files as $file) {
            if (substr($file, -4) == '.zip') {
                $backups[] = [
                    'file_name' => basename($file),
                    'relative_path' => $file,
                    'file_size' => Number::fileSize($disk->size($file), 1),
                    'last_modified' => date('d M Y \a\t H:i', $disk->lastModified($file)),
                ];
            }
        }

        return view('backups', compact('backups'));
    }

    /**
     * Create a database backup.
     */
    public function store()
    {
        try {
            // backup command for only database (spatie/laravel-backup package)
            $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
            
            if ($exitCode !== 0) {
                // shorten to first 500 characters of output, in case of error that is too long
                return redirect()->route('backups.index')->with('error', 'Backup failed. Check logs for details: ' . substr($output, 0, 500));
            }

            return redirect()->route('backups.index')->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
}
