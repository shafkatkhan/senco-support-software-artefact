<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
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
            // check if users exist in the database; if connection fails or table doesn't exist, exception thrown
            if (!User::exists()) {
                return redirect()->route('install.index');
            }
        } catch (\Exception $e) {
            // database not configured or migrated
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
