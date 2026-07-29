<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\AdjustStockRequest;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Models\DropdownOption;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\StorageLocation;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request): View
    {
        $query = InventoryItem::with(['vendor', 'storageLocation']);

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->input('item_type'));
        }

        $items = $query->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $storageLocations = StorageLocation::where('is_active', true)->get();
        $dropdownOptions = $this->getDropdownOptions();

        return view('inventory.index', compact('items', 'vendors', 'storageLocations', 'dropdownOptions'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create(): View
    {
        $vendors = Vendor::where('is_active', true)->get();
        $storageLocations = StorageLocation::where('is_active', true)->get();
        $dropdownOptions = $this->getDropdownOptions();

        return view('inventory.create', compact('vendors', 'storageLocations', 'dropdownOptions'));
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(StoreInventoryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['item_label'] = InventoryItem::generateItemLabel($data);

        if (empty($data['name'])) {
            $data['name'] = InventoryItem::generateDescription($data);
        }

        if (empty($data['sku'])) {
            $data['sku'] = $data['item_label'];
            $suffix = 1;
            while (InventoryItem::where('sku', $data['sku'])->exists()) {
                $data['sku'] = $data['item_label'] . '-' . $suffix;
                $suffix++;
            }
        }

        // Set defaults for nullable numeric fields
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;
        $data['reserved_quantity'] = $data['reserved_quantity'] ?? 0;
        $data['minimum_stock_level'] = $data['minimum_stock_level'] ?? 0;
        $data['unit_price'] = $data['unit_price'] ?? 0;
        $data['last_added_quantity'] = $data['last_added_quantity'] ?? null;

        $inventoryItem = InventoryItem::create($data);

        // Create initial stock transaction if stock quantity > 0
        if ($data['stock_quantity'] > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $inventoryItem->id,
                'type' => 'in',
                'quantity' => $data['stock_quantity'],
                'notes' => 'Initial stock entry',
                'performed_by' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load([
            'vendor',
            'storageLocation',
            'inventoryTransactions' => function ($query) {
                $query->latest()->limit(50);
            },
            'inventoryTransactions.performer',
        ]);

        return view('inventory.show', compact('inventoryItem'));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventoryItem): View
    {
        $vendors = Vendor::where('is_active', true)->get();
        $storageLocations = StorageLocation::where('is_active', true)->get();
        $dropdownOptions = $this->getDropdownOptions();

        return view('inventory.edit', compact('inventoryItem', 'vendors', 'storageLocations', 'dropdownOptions'));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $data = $request->validated();

        $data['item_label'] = InventoryItem::generateItemLabel($data);

        if (empty($data['name'])) {
            $data['name'] = InventoryItem::generateDescription($data);
        }

        if (empty($data['sku'])) {
            $data['sku'] = $data['item_label'];
            $suffix = 1;
            while (InventoryItem::where('sku', $data['sku'])->where('id', '!=', $inventoryItem->id)->exists()) {
                $data['sku'] = $data['item_label'] . '-' . $suffix;
                $suffix++;
            }
        }

        // Track stock quantity changes
        $oldStockQuantity = $inventoryItem->stock_quantity;
        $newStockQuantity = $data['stock_quantity'] ?? $oldStockQuantity;

        $inventoryItem->update($data);

        // Create transaction if stock quantity changed
        if ($newStockQuantity != $oldStockQuantity) {
            $difference = $newStockQuantity - $oldStockQuantity;
            InventoryTransaction::create([
                'inventory_item_id' => $inventoryItem->id,
                'type' => $difference > 0 ? 'in' : 'out',
                'quantity' => abs($difference),
                'notes' => 'Stock adjusted via edit form',
                'performed_by' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.show', $inventoryItem)
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item (soft delete).
     */
    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Adjust the stock of the specified inventory item.
     */
    public function adjustStock(AdjustStockRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        try {
            $this->inventoryService->adjustStock(
                $inventoryItem,
                $request->validated('type'),
                $request->validated('quantity'),
                $request->validated('notes')
            );

            return redirect()->back()
                ->with('success', 'Stock adjusted successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
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
