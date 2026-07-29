<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorageLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of storage locations.
     */
    public function index(): View
    {
        $storageLocations = StorageLocation::withCount('inventoryItems')
            ->latest()
            ->paginate(15);

        return view('admin.storage-locations.index', compact('storageLocations'));
    }

    /**
     * Show the form for creating a new storage location.
     */
    public function create(): View
    {
        return view('admin.storage-locations.create');
    }

    /**
     * Store a newly created storage location.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:191'],
            'code' => ['required', 'max:50', 'unique:storage_locations,code'],
            'description' => ['nullable'],
        ]);

        // Sanitize string inputs
        $validated = array_map(
            fn ($value) => is_string($value) ? strip_tags($value) : $value,
            $validated
        );

        StorageLocation::create($validated);

        return redirect()->route('admin.storage-locations.index')
            ->with('success', 'Storage location created successfully.');
    }

    /**
     * Display the specified storage location.
     */
    public function show(StorageLocation $storageLocation): View
    {
        $storageLocation->load('inventoryItems');

        return view('admin.storage-locations.show', compact('storageLocation'));
    }

    /**
     * Show the form for editing the specified storage location.
     */
    public function edit(StorageLocation $storageLocation): View
    {
        return view('admin.storage-locations.edit', compact('storageLocation'));
    }

    /**
     * Update the specified storage location.
     */
    public function update(Request $request, StorageLocation $storageLocation): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:191'],
            'code' => ['required', 'max:50', 'unique:storage_locations,code,' . $storageLocation->id],
            'description' => ['nullable'],
        ]);

        // Sanitize string inputs
        $validated = array_map(
            fn ($value) => is_string($value) ? strip_tags($value) : $value,
            $validated
        );

        $storageLocation->update($validated);

        return redirect()->route('admin.storage-locations.show', $storageLocation)
            ->with('success', 'Storage location updated successfully.');
    }

    /**
     * Remove the specified storage location.
     */
    public function destroy(StorageLocation $storageLocation): RedirectResponse
    {
        $storageLocation->delete();

        return redirect()->route('admin.storage-locations.index')
            ->with('success', 'Storage location deleted successfully.');
    }
}
