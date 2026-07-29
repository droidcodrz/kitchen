<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVendorRequest;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(): View
    {
        $vendors = Vendor::withCount('inventoryItems')
            ->latest()
            ->paginate(15);

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create(): View
    {
        return view('admin.vendors.create');
    }

    /**
     * Store a newly created vendor.
     */
    public function store(StoreVendorRequest $request): RedirectResponse
    {
        Vendor::create($request->validated());

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): View
    {
        $vendor->load('inventoryItems');

        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor): View
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(StoreVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($request->validated());

        return redirect()->route('admin.vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor (soft delete).
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
