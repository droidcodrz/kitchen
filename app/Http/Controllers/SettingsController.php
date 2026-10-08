<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBrandingRequest;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            $this->forgetLogoSettings();
        }

        if ($request->hasFile('logo')) {
            // Same route as a project attachment: stored on the private disk
            // under its own folder, served back through a controller rather
            // than from the web root.
            $path = $request->file('logo')->store('branding', 'private');
            $disk = Storage::disk('private');

            // Checked for an empty result as well as an outright false. A
            // guard on false alone let a null or empty path through, and the
            // settings were then written as though the upload had worked: the
            // path saved as nothing while the type and version said a logo was
            // there. Nothing appeared and nothing reported a failure.
            //
            // The file is confirmed on disk before anything is recorded, so a
            // write that reports success it did not have cannot leave a
            // setting pointing at a file that is not there.
            if (!is_string($path) || $path === '' || !$disk->exists($path) || $disk->size($path) === 0) {
                return redirect()->back()
                    ->with('error', 'The logo could not be saved. Check that storage/app/private is writable, then try again.');
            }

            // Only after the replacement is safely on disk, so a failed write
            // cannot leave the installation with no logo at all.
            $this->deleteStoredLogo();

            AppSetting::put(AppSetting::KEY_LOGO_PATH, $path);
            AppSetting::put(AppSetting::KEY_LOGO_DISK, 'private');
            // Changes the logo's URL so browsers holding the previous one do
            // not keep showing it.
            AppSetting::put(AppSetting::KEY_LOGO_VERSION, (string) now()->timestamp);
            // Recorded now so serving it never depends on detecting the type,
            // which needs the fileinfo extension. Checked against the allowed
            // list because this value comes from the browser.
            $mime = $request->file('logo')->getClientMimeType();
            AppSetting::put(
                AppSetting::KEY_LOGO_MIME,
                in_array($mime, AppSetting::ALLOWED_LOGO_MIMES, true) ? $mime : null
            );
        }

        return redirect()->back()
            ->with('success', 'Branding updated successfully.');
    }

    /**
     * Clears every setting describing the logo, not just its path.
     *
     * Leaving the type and version behind made it look, to anything reading
     * the table, as though a logo were still set while the path said
     * otherwise - which is exactly the state that is hard to tell apart from
     * an upload that silently failed.
     */
    private function forgetLogoSettings(): void
    {
        AppSetting::put(AppSetting::KEY_LOGO_PATH, null);
        AppSetting::put(AppSetting::KEY_LOGO_MIME, null);
        AppSetting::put(AppSetting::KEY_LOGO_VERSION, null);
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
    public function logo(): BinaryFileResponse
    {
        $path = AppSetting::get(AppSetting::KEY_LOGO_PATH);
        $disk = Storage::disk(AppSetting::logoDisk());

        if ($path === null || !$disk->exists($path)) {
            abort(404);
        }

        // Served with readfile() rather than streamed, matching the attachment
        // download in this application. $disk->response() streams through
        // fpassthru(), which some hosts disable in php.ini - and when it is
        // disabled the response comes back empty with no error anywhere. The
        // file is on disk, every setting is right, and the browser shows a
        // broken image. That trap was already found once here, on attachment
        // downloads; this is the same trap.
        return response()->file($disk->path($path), [
            // Set explicitly rather than left to detection - see
            // AppSetting::logoMimeType for why that matters here.
            'Content-Type' => AppSetting::logoMimeType(),
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
