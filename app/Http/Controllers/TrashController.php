<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFolder;
use App\Models\Project;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Display all soft-deleted items.
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'all');

        $trashedProjects = collect();
        $trashedProducts = collect();
        $trashedCategories = collect();
        $trashedFolders = collect();

        if ($type === 'all' || $type === 'projects') {
            $trashedProjects = Project::onlyTrashed()
                ->with(['client', 'projectManager'])
                ->latest('deleted_at')
                ->get();
        }

        if ($type === 'all' || $type === 'products') {
            $trashedProducts = Product::onlyTrashed()
                ->with('category')
                ->latest('deleted_at')
                ->get();
        }

        if ($type === 'all' || $type === 'categories') {
            $trashedCategories = Category::onlyTrashed()
                ->latest('deleted_at')
                ->get();
        }

        if ($type === 'all' || $type === 'folders') {
            $trashedFolders = ProductFolder::onlyTrashed()
                ->withCount('products')
                ->latest('deleted_at')
                ->get();
        }

        $counts = [
            'projects' => in_array($type, ['all', 'projects']) ? $trashedProjects->count() : Project::onlyTrashed()->count(),
            'products' => in_array($type, ['all', 'products']) ? $trashedProducts->count() : Product::onlyTrashed()->count(),
            'categories' => in_array($type, ['all', 'categories']) ? $trashedCategories->count() : Category::onlyTrashed()->count(),
            'folders' => in_array($type, ['all', 'folders']) ? $trashedFolders->count() : ProductFolder::onlyTrashed()->count(),
        ];

        if ($request->ajax()) {
            return view('trash._list', compact(
                'trashedProjects',
                'trashedProducts',
                'trashedCategories',
                'trashedFolders',
                'type'
            ));
        }

        return view('trash.index', compact(
            'trashedProjects',
            'trashedProducts',
            'trashedCategories',
            'trashedFolders',
            'type',
            'counts'
        ));
    }

    /**
     * Restore a soft-deleted item.
     */
    public function restore(Request $request, string $type, int $id): RedirectResponse|JsonResponse
    {
        $model = $this->getModel($type);

        if (!$model) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid item type.'], 422);
            }

            return redirect()->route('trash.index')
                ->with('error', 'Invalid item type.');
        }

        $item = $model::onlyTrashed()->findOrFail($id);
        $item->restore();

        // Deleting a confirmed project hands its reserved materials back to
        // inventory, so bringing the project back has to take them again -
        // otherwise the project is live once more while nothing is held for
        // it, and the available quantity reads higher than it really is.
        //
        // Only 'confirmed' holds a reservation: draft never had one, and
        // in_production and later already consumed their stock and were not
        // released on delete, so they must be left alone here too.
        if ($item instanceof Project && $item->status === 'confirmed') {
            $this->inventoryService->reserveForProject(
                $item->load('products.requiredMaterials', 'inventoryItems')
            );
        }

        $typeName = $this->getTypeName($type);

        if ($request->wantsJson()) {
            return response()->json(['message' => "{$typeName} restored successfully."]);
        }

        return redirect()->route('trash.index')
            ->with('success', "{$typeName} restored successfully.");
    }

    /**
     * Permanently delete a soft-deleted item.
     */
    public function forceDestroy(Request $request, string $type, int $id): RedirectResponse
    {
        $model = $this->getModel($type);

        if (!$model) {
            return redirect()->route('trash.index')
                ->with('error', 'Invalid item type.');
        }

        $item = $model::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        $typeName = $this->getTypeName($type);

        return redirect()->route('trash.index')
            ->with('success', "{$typeName} permanently deleted.");
    }

    /**
     * Get the model class based on type.
     */
    private function getModel(string $type): ?string
    {
        return match ($type) {
            'project' => Project::class,
            'product' => Product::class,
            'category' => Category::class,
            'folder' => ProductFolder::class,
            default => null,
        };
    }

    /**
     * Get friendly type name.
     */
    private function getTypeName(string $type): string
    {
        return match ($type) {
            'project' => 'Project',
            'product' => 'Product',
            'category' => 'Category',
            'folder' => 'Folder',
            default => 'Item',
        };
    }
}
