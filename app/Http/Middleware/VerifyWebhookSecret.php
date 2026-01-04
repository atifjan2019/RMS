<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSecret
{
    /**
     * Verify webhook secret for Pub/Sub endpoints.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.webhook.secret');

        // Check header or query param
        $providedSecret = $request->header('X-Webhook-Secret')
            ?? $request->query('secret');

        if (!$secret || $providedSecret !== $secret) {
            abort(403, 'Invalid webhook secret');
        }

        return $next($request);
    }
}
