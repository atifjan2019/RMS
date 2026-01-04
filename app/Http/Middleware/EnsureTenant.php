<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    /**
     * Ensure the user has a tenant and set it in the container.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $tenant = $user->tenant;

        if (!$tenant) {
            // This shouldn't happen if registration creates tenant properly
            abort(403, 'No tenant associated with your account.');
        }

        // Bind tenant to container for easy access
        app()->instance('tenant', $tenant);

        // Share with views
        view()->share('tenant', $tenant);

        return $next($request);
    }
}
