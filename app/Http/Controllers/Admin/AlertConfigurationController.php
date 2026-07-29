<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertConfiguration;
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

        return view('admin.alert-configurations.index', compact('alertConfigurations'));
    }

    /**
     * Update the specified alert configuration.
     */
    public function update(Request $request, AlertConfiguration $alertConfiguration): RedirectResponse
    {
        $validated = $request->validate([
            'threshold_value' => ['required', 'numeric', 'min:0'],
            'is_enabled' => ['required', 'boolean'],
            'notify_roles' => ['nullable', 'array'],
            'notify_roles.*' => ['exists:roles,id'],
        ]);

        $alertConfiguration->update($validated);

        return redirect()->back()
            ->with('success', 'Alert configuration updated successfully.');
    }
}
