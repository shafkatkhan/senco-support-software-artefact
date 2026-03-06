<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Ifsnop\Mysqldump as IMysqldump;

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
            if (substr($file, -4) == '.sql') {
                $backups[] = [
                    'file_name' => basename($file),
                    'relative_path' => $file,
                    'file_size' => Number::fileSize($disk->size($file), 1),
                    'last_modified' => $disk->lastModified($file),
                ];
            }
        }

        $title = 'System Backups';
        return view('backups', compact('backups', 'title'));
    }

    /**
     * Create a database backup file.
     */
    public function store()
    {
        try {
            $db = config('database.connections.mysql');
            $database_connection = "mysql:host={$db['host']};dbname={$db['database']}";
            
            $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
            $directory = env('APP_NAME', 'laravel-backup');
            
            // ensure directory exists in storage
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }
            
            // generate absolute path
            $path = Storage::disk('local')->path("{$directory}/{$fileName}");

            // create dump
            $dump = new IMysqldump\Mysqldump($database_connection, $db['username'], $db['password']);
            $dump->start($path);

            return redirect()->route('backups.index')->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download($file_path)
    {
        $file = urldecode($file_path);
        $disk = Storage::disk('local');

        if ($disk->exists($file)) {
            return response()->download($disk->path($file));
        }

        return redirect()->route('backups.index')->with('error', 'Backup file not found.');
    }

    /**
     * Delete a backup file.
     */
    public function destroy($file_path)
    {
        $file = urldecode($file_path);
        $disk = Storage::disk('local');

        if ($disk->exists($file)) {
            $disk->delete($file);
            return redirect()->route('backups.index')->with('success', 'Backup deleted successfully!');
        }

        return redirect()->route('backups.index')->with('error', 'Backup file not found.');
    }
}
