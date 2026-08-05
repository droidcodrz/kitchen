<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductFolderController extends Controller
{
    /**
     * Display a listing of folders.
     */
    public function index(): View
    {
        $folders = ProductFolder::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('product-folders.index', compact('folders'));
    }

    /**
     * Store a newly created folder.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['user_id'] = auth()->id();

        $folder = ProductFolder::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $folder->id,
                'name' => $folder->name,
                'slug' => $folder->slug,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Folder created successfully.');
    }

    /**
     * Show the form for editing the specified folder.
     */
    public function edit(ProductFolder $productFolder): View
    {
        return view('product-folders.edit', compact('productFolder'));
    }

    /**
     * Update the specified folder.
     */
    public function update(Request $request, ProductFolder $productFolder): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $productFolder->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $productFolder->id,
                'name' => $productFolder->name,
                'slug' => $productFolder->slug,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Folder updated successfully.');
    }

    /**
     * Remove the specified folder (soft delete).
     */
    public function destroy(ProductFolder $productFolder): RedirectResponse
    {
        // Move products to no folder before deleting
        $productFolder->products()->update(['folder_id' => null]);

        $productFolder->delete();

        return redirect()->back()
            ->with('success', 'Folder deleted successfully. Products moved to "All Products".');
    }

    /**
     * Move a product to a folder.
     */
    public function moveProduct(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'folder_id' => ['nullable', 'exists:product_folders,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->update(['folder_id' => $validated['folder_id']]);

        $folderName = $validated['folder_id']
            ? ProductFolder::find($validated['folder_id'])->name
            : 'Uncategorized';

        if ($request->wantsJson()) {
            return response()->json(['message' => "Product moved to \"{$folderName}\" successfully."]);
        }

        return redirect()->back()
            ->with('success', "Product moved to \"{$folderName}\" successfully.");
    }
}
