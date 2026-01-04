<?php

namespace App\Http\Controllers;

use App\Models\GoogleConnection;
use App\Jobs\SyncLocationsJob;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleConnectController extends Controller
{
    /**
     * Show the Google connect page.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;
        $connection = $tenant->googleConnection;

        return view('google.connect', [
            'isConnected' => (bool) $connection,
            'connection' => $connection,
        ]);
    }

    /**
     * Redirect to Google OAuth.
     */
    public function connect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        $scopes = [
            'https://www.googleapis.com/auth/business.manage',
            'email',
            'profile',
        ];

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'access_type' => 'offline',
            'prompt' => 'consent', // Force consent to get refresh token
            'state' => csrf_token(),
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    /**
     * Handle Google OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        // Check for errors
        if ($request->has('error')) {
            Log::error('Google OAuth error', ['error' => $request->get('error')]);
            return redirect()->route('google.index')
                ->with('error', 'Failed to connect Google: ' . $request->get('error'));
        }

        $code = $request->get('code');

        if (!$code) {
            return redirect()->route('google.index')
                ->with('error', 'No authorization code received.');
        }

        try {
            // Exchange code for tokens
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                Log::error('Google token exchange failed', ['response' => $response->json()]);
                return redirect()->route('google.index')
                    ->with('error', 'Failed to exchange authorization code.');
            }

            $tokens = $response->json();

            // Get user info
            $userInfo = Http::withToken($tokens['access_token'])
                ->get('https://www.googleapis.com/oauth2/v2/userinfo')
                ->json();

            $tenant = auth()->user()->tenant;

            // Create or update Google connection
            GoogleConnection::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'google_subject' => $userInfo['id'] ?? null,
                    'email' => $userInfo['email'] ?? null,
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                    'scopes' => explode(' ', $tokens['scope'] ?? ''),
                ]
            );

            // Dispatch job to sync locations
            SyncLocationsJob::dispatch($tenant->id)->onQueue('google');

            return redirect()->route('locations.index')
                ->with('success', 'Google Business Profile connected! Syncing your locations...');

        } catch (\Exception $e) {
            Log::error('Google OAuth exception', ['error' => $e->getMessage()]);
            return redirect()->route('google.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google account.
     */
    public function disconnect(): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $connection = $tenant->googleConnection;

        if ($connection) {
            // Optionally revoke tokens
            try {
                app(\App\Services\GoogleTokenService::class)->revokeTokens($connection);
            } catch (\Exception $e) {
                // Ignore revoke errors
            }

            $connection->delete();
        }

        return redirect()->route('google.index')
            ->with('success', 'Google account disconnected.');
    }
}
