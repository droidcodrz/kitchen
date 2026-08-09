<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertConfiguration;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertConfigurationController extends Controller
{
    /**
     * Display a listing of alert configurations.
     */
    public function index(): View
    {
        $alertConfigurations = AlertConfiguration::paginate(15);
        $roles = Role::orderBy('name')->get();

        return view('admin.alert-configurations.index', compact('alertConfigurations', 'roles'));
    }

    /**
     * Update the specified alert configuration.
     */
    public function update(Request $request, AlertConfiguration $alertConfiguration): RedirectResponse
    {
        $validated = $request->validate([
            'threshold_value' => ['required', 'numeric', 'min:0'],
            'is_enabled' => ['required', 'boolean'],
            'notify_via_email' => ['required', 'boolean'],
            'notify_roles' => ['nullable', 'array'],
            'notify_roles.*' => ['exists:roles,slug'],
            'always_notify_emails' => ['nullable', 'array'],
            'always_notify_emails.*' => ['email'],
        ]);

        $alertConfiguration->update($validated);

        return redirect()->back()
            ->with('success', 'Alert configuration updated successfully.');
    }
}
