<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class EnsureMfaIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // verify the user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        // verify the user has a pending MFA setup requirement
        if (!$request->user()->isMfaPending()) {
            return $next($request);
        }

        // exclude routes that need to be accessible even if MFA is unverified
        $excludedRoutes = [
            'mfa-setup.index',
            'mfa-setup.verify',
            'mfa-setup.reset',
            'mfa-challenge.index',
            'mfa-challenge.verify',
            'logout'
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        if (!$request->user()->mfa_verified_at) {
            // MFA setup is required
            return redirect()->route('mfa-setup.index')->with('warning', __('Please complete your MFA setup before continuing.'));
        }

        // MFA challenge is required
        return redirect()->route('mfa-challenge.index');
    }
}
