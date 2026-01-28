<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function index() {
        return view('install');
    }

    public function process(Request $request) {

        $request->validate([
            'app_url' => 'required|url',
            'db_host' => 'required',
            'db_port' => 'required',
            'db_name' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        try {
            // update runtime configuration to use new credentials
            config(['database.default' => 'mysql']);
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.port' => $request->db_port]);
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_username]);
            config(['database.connections.mysql.password' => $request->db_password]);

            // force reconnection and verify connection
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::connection()->getPdo();

            // run migrations and seed
            Artisan::call('migrate:fresh', ['--seed' => true]);

            // update .env
            $this->updateEnv([
                'APP_URL' => $request->app_url,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ]);

            return redirect(route('login'))->with('success', 'Installation successful! Please login.');

        } catch (\Exception $e) {
            return back()->with('error', 'Installation failed: ' . $e->getMessage())->withInput();
        }
    }

    private function updateEnv($data = [])
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $file_content = File::get($path);
            foreach ($data as $key => $value) {
                $value = $value ?? ''; // set null value to empty string
                $value = '"' . $value . '"';
                
                // regex to find the key and replace value
                $pattern = "/^" . preg_quote($key) . "=(.*)$/m";
                
                if (preg_match($pattern, $file_content)) {
                    $file_content = preg_replace_callback($pattern, function () use ($key, $value) {
                         return "$key=$value";
                    }, $file_content);
                } else {
                    $file_content .= "\n$key=$value"; // append it if not existing
                }
            }
            File::put($path, $file_content);
        }
    }
}
