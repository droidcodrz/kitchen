<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        return view('settings.index');
    }

    /**
     * Update the authenticated user's theme preference.
     */
    public function updateTheme(Request $request): RedirectResponse|JsonResponse
    {
        // 'system' was accepted here but the column is an enum of light and dark
        // only, so a request for it passed validation and then failed at the
        // database. Nothing offers the option, so the rule now matches what can
        // actually be stored rather than promising a third choice.
        $validated = $request->validate([
            'theme_preference' => ['required', 'in:light,dark'],
        ]);

        $request->user()->update([
            'theme_preference' => $validated['theme_preference'],
        ]);

        // The theme switches on the client the moment it is clicked and this
        // call only records it, so the caller wants an acknowledgement, not a
        // redirect that would throw away the page they are on.
        if ($request->expectsJson()) {
            return response()->json(['theme_preference' => $validated['theme_preference']]);
        }

        return redirect()->back()
            ->with('success', 'Theme preference updated successfully.');
    }
}
