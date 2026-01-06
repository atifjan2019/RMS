<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;

        return view('settings.index', [
            'tenant' => $tenant,
            'tones' => ['friendly', 'professional', 'recovery'],
        ]);
    }

    /**
     * Update auto-reply settings.
     */
    public function updateAutoReply(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'auto_reply_enabled' => 'boolean',
            'auto_reply_tone' => 'nullable|array',
            'auto_reply_tone.*' => 'string|in:friendly,professional,recovery',
            'auto_reply_stars' => 'nullable|array',
            'auto_reply_stars.*' => 'integer|min:1|max:5',
            'auto_reply_delay_minutes' => 'required|integer|min:1|max:1440',
        ]);

        $tenant->update([
            'auto_reply_enabled' => $request->boolean('auto_reply_enabled'),
            'auto_reply_tone' => $validated['auto_reply_tone'] ?? [],
            'auto_reply_stars' => $validated['auto_reply_stars'] ?? [],
            'auto_reply_delay_minutes' => $validated['auto_reply_delay_minutes'],
        ]);

        return back()->with('success', 'Auto-reply settings saved successfully.');
    }
}
