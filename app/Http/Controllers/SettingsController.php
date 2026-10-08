<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBrandingRequest;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Update the application name and logo.
     *
     * These belong to the installation rather than to whoever is signed in -
     * they are what the login screen and the sidebar show everyone - so this
     * is restricted to administrators.
     */
    public function updateBranding(UpdateBrandingRequest $request): RedirectResponse
    {
        if ($request->filled('app_name')) {
            AppSetting::put(AppSetting::KEY_APP_NAME, trim($request->input('app_name')));
        } else {
            // Cleared on purpose: fall back to the default rather than leaving
            // the application with no name anywhere on screen.
            AppSetting::put(AppSetting::KEY_APP_NAME, null);
        }

        if ($request->boolean('remove_logo')) {
            $this->deleteStoredLogo();
            AppSetting::put(AppSetting::KEY_LOGO_PATH, null);
        }

        if ($request->hasFile('logo')) {
            // Same route as a project attachment: stored on the private disk
            // under its own folder, served back through a controller rather
            // than from the web root.
            $path = $request->file('logo')->store('branding', 'private');

            if ($path === false) {
                return redirect()->back()
                    ->with('error', 'The logo could not be saved. Please try again.');
            }

            // Only after the replacement is safely on disk, so a failed write
            // cannot leave the installation with no logo at all.
            $this->deleteStoredLogo();

            AppSetting::put(AppSetting::KEY_LOGO_PATH, $path);
            AppSetting::put(AppSetting::KEY_LOGO_DISK, 'private');
            // Changes the logo's URL so browsers holding the previous one do
            // not keep showing it.
            AppSetting::put(AppSetting::KEY_LOGO_VERSION, (string) now()->timestamp);
        }

        return redirect()->back()
            ->with('success', 'Branding updated successfully.');
    }

    /**
     * Removes the logo file currently on disk, if there is one. Missing files
     * are not an error here - the point is only that it is gone afterwards.
     */
    private function deleteStoredLogo(): void
    {
        $path = AppSetting::get(AppSetting::KEY_LOGO_PATH);

        if ($path === null) {
            return;
        }

        Storage::disk(AppSetting::logoDisk())->delete($path);
    }

    /**
     * Serve the uploaded logo.
     *
     * Deliberately outside auth: the login screen shows the logo, and a
     * sign-in page cannot fetch an image that requires being signed in. Only
     * ever streams the one stored path, never a path from the request.
     */
    public function logo(): Response|StreamedResponse
    {
        $path = AppSetting::get(AppSetting::KEY_LOGO_PATH);
        $disk = Storage::disk(AppSetting::logoDisk());

        if ($path === null || !$disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
