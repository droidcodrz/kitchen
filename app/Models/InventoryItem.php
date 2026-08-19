<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    /**
     * Project statuses that count as done. A project in any other status is
     * still running, so whatever it depends on must not be deleted out from
     * under it. Shared with Product so the two rules cannot drift apart.
     */
    public const CLOSED_PROJECT_STATUSES = ['delivered', 'finished'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'item_label',
        'sku',
        'item_type',
        'inventory_type',
        'material_type',
        'material_grade',
        'thickness_gauge',
        'thickness_mm',
        'dimension',
        'diameter',
        'stock_quantity',
        'reserved_quantity',
        'minimum_stock_level',
        'unit_price',
        'unit_sale_price',
        'unit_of_measure',
        'vendor_id',
        'storage_location_id',
        'last_added_quantity',
        'last_added_at',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:2',
            'reserved_quantity' => 'decimal:2',
            'minimum_stock_level' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'unit_sale_price' => 'decimal:2',
            'thickness_mm' => 'decimal:4',
            'last_added_quantity' => 'decimal:2',
            'last_added_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    private const MATERIAL_ABBREVIATIONS = [
        'Stainless Steel' => 'SS',
        'Aluminum' => 'AL',
        'Galvanize Steel' => 'GS',
        'Black Iron' => 'BI',
        'Silicon' => 'SI',
        'Fire Wrap' => 'FW',
        'Glue' => 'GL',
    ];

    private const ITEM_TYPE_ABBREVIATIONS = [
        'Sheet Metal' => 'S',
        'Metal Accessory' => 'MA',
        'Miscellaneous' => 'MISC',
        'Restaurant Fan' => 'FAN',
        'Food Truck Fan' => 'FANFT',
    ];

    public static function generateItemLabel(array $data): string
    {
        $label = '';

        $materialType = $data['material_type'] ?? '';
        $label .= self::MATERIAL_ABBREVIATIONS[$materialType] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $materialType), 0, 4));

        $itemType = $data['item_type_label'] ?? '';
        $label .= self::ITEM_TYPE_ABBREVIATIONS[$itemType] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $itemType), 0, 3));

        if (!empty($data['material_grade'])) {
            $label .= 'GR' . $data['material_grade'];
        }

        if (!empty($data['thickness_gauge'])) {
            $label .= 'GA' . $data['thickness_gauge'];
        } elseif (!empty($data['thickness_mm'])) {
            $label .= 'MM' . $data['thickness_mm'];
        }

        if (!empty($data['dimension'])) {
            $parts = preg_split('/[xX×]/', $data['dimension']);
            if (count($parts) === 2) {
                $label .= 'W' . trim($parts[0]) . 'L' . trim($parts[1]);
            }
        }

        return $label;
    }

    public static function generateDescription(array $data): string
    {
        $parts = [];

        if (!empty($data['material_type'])) {
            $parts[] = $data['material_type'];
        }

        if (!empty($data['item_type_label'])) {
            $parts[] = $data['item_type_label'];
        }

        if (!empty($data['material_grade'])) {
            $parts[] = 'Grade ' . $data['material_grade'];
        }

        if (!empty($data['thickness_gauge'])) {
            $parts[] = 'Gauge ' . $data['thickness_gauge'];
        }

        if (!empty($data['thickness_mm'])) {
            $parts[] = '(' . $data['thickness_mm'] . ' mm)';
        }

        if (!empty($data['dimension'])) {
            $dimParts = preg_split('/[xX×]/', $data['dimension']);
            if (count($dimParts) === 2) {
                $parts[] = 'Width ' . trim($dimParts[0]) . ' ft Length ' . trim($dimParts[1]) . ' ft';
            } else {
                $parts[] = $data['dimension'];
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Get the vendor for this inventory item.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the storage location for this inventory item.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }

    /**
     * Get the products that require this inventory item as a material.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_material')
            ->withPivot('quantity_required', 'notes');
    }

    /**
     * Projects this item is attached to directly, outside of any product.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_inventory_item')
            ->withPivot('quantity', 'notes');
    }

    /**
     * How many still-running projects depend on this item - either attached
     * to the project directly, or pulled in through the bill of materials of
     * a product on that project. Counted over projects rather than over the
     * two paths separately, so a project using it both ways counts once.
     *
     * Deleting an item underneath these projects would leave their reserved
     * and consumed stock referring to something that no longer exists.
     */
    public function activeProjectsCount(): int
    {
        return Project::whereNotIn('projects.status', self::CLOSED_PROJECT_STATUSES)
            ->where(function ($query) {
                $query->whereHas('inventoryItems', fn ($q) => $q->where('inventory_items.id', $this->getKey()))
                    ->orWhereHas('products.requiredMaterials', fn ($q) => $q->where('inventory_items.id', $this->getKey()));
            })
            ->count();
    }

    /**
     * Get the inventory transactions for this item.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Get the available quantity (stock minus reserved).
     */
    public function getAvailableQuantityAttribute(): float
    {
        return $this->stock_quantity - $this->reserved_quantity;
    }

    /**
     * Determine if the item is low on stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity < $this->minimum_stock_level;
    }

    /**
     * Get the stock status of the item.
     */
    public function getStatusAttribute(): string
    {
        return $this->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';
    }

    /**
     * Get custom field values for this inventory item.
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'entity_id')
            ->where('entity_type', 'inventory_item');
    }

    /**
     * Get a custom field value by field name.
     */
    public function getCustomFieldValue(string $fieldName)
    {
        $fieldValue = $this->customFieldValues()
            ->whereHas('definition', fn($q) => $q->where('field_name', $fieldName))
            ->first();

        return $fieldValue ? $fieldValue->value : null;
    }

    /**
     * Set a custom field value.
     */
    public function setCustomFieldValue(int $definitionId, $value): void
    {
        CustomFieldValue::updateOrCreate(
            [
                'custom_field_definition_id' => $definitionId,
                'entity_type' => 'inventory_item',
                'entity_id' => $this->id,
            ],
            ['value' => $value]
        );
    }
}
