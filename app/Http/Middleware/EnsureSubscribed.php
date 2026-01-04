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
