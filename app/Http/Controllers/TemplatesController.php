<?php

namespace App\Http\Controllers;

use App\Models\ReplyTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplatesController extends Controller
{
    /**
     * List all templates.
     */
    public function index(): View
    {
        $tenant = auth()->user()->tenant;

        $templates = $tenant->replyTemplates()
            ->orderBy('tone')
            ->orderBy('name')
            ->get();

        return view('templates.index', [
            'templates' => $templates,
            'tones' => ReplyTemplate::tones(),
        ]);
    }

    /**
     * Store a new template.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tone' => 'required|in:friendly,professional,recovery',
            'body' => 'required|string|max:4096',
        ]);

        $tenant->replyTemplates()->create($validated);

        return back()->with('success', 'Template created successfully.');
    }

    /**
     * Update a template.
     */
    public function update(Request $request, ReplyTemplate $template): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        if ($template->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tone' => 'required|in:friendly,professional,recovery',
            'body' => 'required|string|max:4096',
        ]);

        $template->update($validated);

        return back()->with('success', 'Template updated successfully.');
    }

    /**
     * Delete a template.
     */
    public function destroy(ReplyTemplate $template): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        if ($template->tenant_id !== $tenant->id) {
            abort(403);
        }

        $template->delete();

        return back()->with('success', 'Template deleted.');
    }
}
