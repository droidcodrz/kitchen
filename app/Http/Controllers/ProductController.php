<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\DropdownOption;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        // Handle view preference
        if ($request->has('view')) {
            session(['products_view' => $request->get('view')]);
        }
        $view = session('products_view', 'grid');
        $perPage = $view === 'table' ? 25 : 15;

        // Only 'category' is ever displayed on this list (grid view); 'requiredMaterials'
        // and 'folder' were being eager-loaded here too but are never accessed by
        // products/_main.blade.php - that was a many-to-many pivot query and a belongsTo
        // load, wasted on every product on every page.
        $query = Product::with('category');

        // Filter by folder
        if ($request->has('folder')) {
            if ($request->get('folder') === 'none') {
                $query->whereNull('folder_id');
            } else {
                $query->where('folder_id', $request->get('folder'));
            }
        }

        $products = $query->latest()->paginate($perPage);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->get(['id', 'name']);
        $folders = \App\Models\ProductFolder::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // The folder sidebar's "All Products" / "Uncategorized" counts used to be two
        // raw Product::count() queries run directly inside the Blade view, re-executed
        // on every page load and every AJAX list refresh (e.g. after moving a product
        // between folders). Compute both here in a single query instead.
        $productCounts = Product::selectRaw('COUNT(*) as total, SUM(CASE WHEN folder_id IS NULL THEN 1 ELSE 0 END) as uncategorized')->first();
        $totalProductsCount = (int) $productCounts->total;
        $uncategorizedProductsCount = (int) $productCounts->uncategorized;

        if ($request->ajax()) {
            return view('products._main', compact('products', 'folders', 'view', 'totalProductsCount', 'uncategorizedProductsCount'));
        }

        $dropdownOptions = $this->getDropdownOptions();

        return view('products.index', compact('products', 'categories', 'inventoryItems', 'view', 'folders', 'dropdownOptions', 'totalProductsCount', 'uncategorizedProductsCount'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        // Custom field definitions per category are fetched separately via the
        // getCustomFields() AJAX endpoint once a category is picked - eager-loading
        // them here was never read by products/create.blade.php.
        $categories = Category::where('is_active', true)->get();
        // Only id/name are ever shown (the Required Materials checklist) - no need
        // to pull every column (spec fields, stock, timestamps) for every item.
        $inventoryItems = InventoryItem::where('is_active', true)->get(['id', 'name']);
        $folders = \App\Models\ProductFolder::orderBy('name')->get();

        $dropdownOptions = $this->getDropdownOptions();

        return view('products.create', compact('categories', 'inventoryItems', 'folders', 'dropdownOptions'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['item_label'] = Product::generateItemLabel($data);

        if (empty($data['name'])) {
            $data['name'] = Product::generateDescription($data);
        }

        if (!empty($data['name_suffix'])) {
            $data['name'] .= ' - ' . $data['name_suffix'];
        }
        unset($data['name_suffix']);

        $data['slug'] = Str::slug($data['name']);

        $product = $this->createProductRetryingOnSkuCollision($data);

        if (!empty($data['material_ids'])) {
            $product->requiredMaterials()->sync(
                $this->buildMaterialSyncData($data['material_ids'], $request->input('material_quantities', []))
            );
        }

        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields', []) as $fieldId => $value) {
                if (!empty($value)) {
                    $product->setCustomFieldValue($fieldId, $value);
                }
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'requiredMaterials.vendor', 'projects', 'customFieldValues.definition']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load('requiredMaterials', 'customFieldValues.definition');

        $categories = Category::where('is_active', true)->get();
        $inventoryItems = InventoryItem::where('is_active', true)->get(['id', 'name']);
        $folders = \App\Models\ProductFolder::orderBy('name')->get();

        $dropdownOptions = $this->getDropdownOptions();

        return view('products.edit', compact('product', 'categories', 'inventoryItems', 'folders', 'dropdownOptions'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $data['item_label'] = Product::generateItemLabel($data);

        if (empty($data['name'])) {
            $data['name'] = Product::generateDescription($data);
        }

        if (!empty($data['name_suffix'])) {
            $data['name'] .= ' - ' . $data['name_suffix'];
        }
        unset($data['name_suffix']);

        // slug is uniquely indexed just like sku, so it needs the same
        // collision handling - renaming a product onto an existing slug
        // would otherwise fail at the database as a 500.
        $baseSlug = Str::slug($data['name']);
        $data['slug'] = $baseSlug;
        $suffix = 1;
        while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
            $data['slug'] = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        if (empty($data['sku'])) {
            $data['sku'] = $data['item_label'];
            $suffix = 1;
            while (Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->exists()) {
                $data['sku'] = $data['item_label'] . '-' . $suffix;
                $suffix++;
            }
        }

        $product->update($data);

        // The Required Materials checklist is always rendered on this form
        // (never conditionally hidden), so always sync it to whatever's
        // checked now - including nothing. Unchecked HTML checkboxes submit
        // no field at all, so guarding this on "was material_ids present in
        // the request" meant unchecking every material silently failed to
        // detach them: the sync call never ran.
        $product->requiredMaterials()->sync(
            $this->buildMaterialSyncData($data['material_ids'] ?? [], $request->input('material_quantities', []))
        );

        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields', []) as $fieldId => $value) {
                if (!empty($value)) {
                    $product->setCustomFieldValue($fieldId, $value);
                } else {
                    \App\Models\CustomFieldValue::where('custom_field_definition_id', $fieldId)
                        ->where('entity_type', 'product')
                        ->where('entity_id', $product->id)
                        ->delete();
                }
            }
        }

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Auto-generate a unique SKU (if none was submitted) and create the
     * product, retrying with the next suffix if a concurrent request (e.g.
     * a double-clicked "Add Product") already took the SKU we checked for
     * a moment ago - the exists() check and the insert aren't atomic, so
     * that race is otherwise a real 500 on the second of two near-
     * simultaneous submits, not just a theoretical one.
     */
    private function createProductRetryingOnSkuCollision(array $data): Product
    {
        $userSuppliedSku = !empty($data['sku']);
        $baseSlug = $data['slug'];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            if (!$userSuppliedSku) {
                $data['sku'] = $attempt === 0
                    ? $data['item_label']
                    : $data['item_label'] . '-' . $attempt;
            }

            // slug carries its own unique index, and two products can legitimately
            // generate the same one (same auto-generated description). Suffix it
            // alongside the SKU so a collision on either is retried, not 500'd.
            $data['slug'] = $attempt === 0 ? $baseSlug : $baseSlug . '-' . $attempt;

            try {
                return Product::create($data);
            } catch (\Illuminate\Database\QueryException $e) {
                $isDuplicate = $e->getCode() === '23000'
                    && (str_contains($e->getMessage(), 'sku') || str_contains($e->getMessage(), 'slug'));

                if (!$isDuplicate || $attempt === 4) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Pair each selected material ID with its quantity_required for sync(),
     * defaulting to 1 when no quantity was submitted for that material.
     */
    private function buildMaterialSyncData(array $materialIds, array $quantities): array
    {
        $syncData = [];

        foreach ($materialIds as $materialId) {
            $syncData[$materialId] = [
                'quantity_required' => $quantities[$materialId] ?? 1,
            ];
        }

        return $syncData;
    }

    /**
     * Quick-add a new category (returns JSON for AJAX).
     */
    public function quickAddCategory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    /**
     * Remove the specified product (soft delete).
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Get custom fields for a category (AJAX endpoint).
     */
    public function getCustomFields(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');
        $itemType = $request->input('item_type');

        if (!$categoryId) {
            return response()->json(['fields' => []]);
        }

        $query = \App\Models\CustomFieldDefinition::where('entity_type', 'product')
            ->where('is_active', true)
            ->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereNull('category_id');
            })
            ->orderBy('sort_order')
            ->orderBy('field_label');

        $fields = $query->get()->filter(function ($field) use ($itemType) {
            return $field->appliesToItemType($itemType);
        })->values();

        return response()->json(['fields' => $fields]);
    }

    private function getDropdownOptions(): array
    {
        return [
            'item_types' => DropdownOption::getOptions(DropdownOption::TYPE_ITEM_TYPE),
            'material_types' => DropdownOption::getOptions(DropdownOption::TYPE_MATERIAL_TYPE),
            'material_grades' => DropdownOption::getOptions(DropdownOption::TYPE_MATERIAL_GRADE),
            'thickness_gauges' => DropdownOption::getOptions(DropdownOption::TYPE_THICKNESS_GAUGE),
            'inventory_types' => DropdownOption::getOptions(DropdownOption::TYPE_INVENTORY_TYPE),
            'system_categories' => DropdownOption::getOptions(DropdownOption::TYPE_SYSTEM_CATEGORY),
        ];
    }
}
