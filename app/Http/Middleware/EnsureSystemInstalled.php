<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use App\Support\InstallState;

class EnsureSystemInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // whitelist installation routes
        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        try {
            // check if language setup is pending
            if (InstallState::isLanguageSetupPending()) {
                return redirect(route('install.lang_setup_view'));
            }

            // check if system is installed
            if (InstallState::isInstalled()) {
                return $next($request);
            }

            // if system is not marked as installed (in case of cache clear), but users table exists, mark as installed (to restore the cache)
            if (Schema::hasTable('users')) {
                InstallState::markInstalled();
                return $next($request);
            }

            // if system is not marked as installed, and users table does not exist, reset and redirect to install
            InstallState::reset();
            return redirect()->route('install.index');

        } catch (\Throwable $e) {
            // database not configured or migrated yet; continue to installation.
            InstallState::reset();
            return redirect()->route('install.index');
        }
    }
}
