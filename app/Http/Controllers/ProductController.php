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

        // $query = Product::with(['category', 'requiredMaterials', 'folder']);
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
        $folders = \App\Models\ProductFolder::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

            $productCounts = Product::selectRaw('COUNT(*) as total, SUM(CASE WHEN folder_id IS NULL THEN 1 ELSE 0 END) as uncategorized')->first();
            $totalProductsCount = (int) $productCounts->total;
            $uncategorizedProductsCount = (int) $productCounts->uncategorized;

            if ($request->ajax()) {
                return view('products._main', compact('products', 'folders', 'view', 'totalProductsCount', 'uncategorizedProductsCount'));
            }

            $dropdownOptions = $this->getDropdownOptions();

            return view('products.index', compact('products', 'categories', 'view', 'folders', 'dropdownOptions', 'totalProductsCount', 'uncategorizedProductsCount'));

        // if ($request->ajax()) {
        //     return view('products._main', compact('products', 'folders', 'view'));
        // }

        // $dropdownOptions = $this->getDropdownOptions();

        // return view('products.index', compact('products', 'categories', 'view', 'folders', 'dropdownOptions'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        // $categories = Category::with('customFieldDefinitions')->where('is_active', true)->get();
        // $inventoryItems = InventoryItem::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
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

        if (empty($data['sku'])) {
            $data['sku'] = $data['item_label'];
            $suffix = 1;
            while (Product::where('sku', $data['sku'])->exists()) {
                $data['sku'] = $data['item_label'] . '-' . $suffix;
                $suffix++;
            }
        }

        $product = Product::create($data);

        if (!empty($data['material_ids'])) {
            $product->requiredMaterials()->sync($data['material_ids']);
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

        // $categories = Category::with('customFieldDefinitions')->where('is_active', true)->get();
        // $inventoryItems = InventoryItem::where('is_active', true)->get();
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

        $data['slug'] = Str::slug($data['name']);

        if (empty($data['sku'])) {
            $data['sku'] = $data['item_label'];
            $suffix = 1;
            while (Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->exists()) {
                $data['sku'] = $data['item_label'] . '-' . $suffix;
                $suffix++;
            }
        }

        $product->update($data);

        if (array_key_exists('material_ids', $data)) {
            $product->requiredMaterials()->sync($data['material_ids'] ?? []);
        }

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
