<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Ensure the user has an active subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Local/dev bypass: allow the seeded admin user to access the app without billing.
        if ($user && $user->email === 'admin@example.com') {
            return $next($request);
        }

        if (!$user || !$user->tenant) {
            return redirect()->route('pricing');
        }

        $tenant = $user->tenant;

        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('billing.plan')
                ->with('warning', 'Please subscribe to access this feature.');
        }

        return $next($request);
    }
}
