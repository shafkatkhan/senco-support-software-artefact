<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class EnsureSystemInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // whitelist installation routes and static assets
        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        try {
            // check cache first to avoid DB query on every request
            if (Cache::get('system_installed')) {
                return $next($request);
            }

            // verify against DB if not in cache
            if (User::exists()) {
                // cache it for next time
                Cache::forever('system_installed', true);
                return $next($request);
            }

            return redirect()->route('install.index');

        } catch (\Exception $e) {
            // database not configured or migrated
            return redirect()->route('install.index');
        }
    }
}
