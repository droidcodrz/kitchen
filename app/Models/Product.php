<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'item_label',
        'slug',
        'sku',
        'category_id',
        'folder_id',
        'type',
        'inventory_type',
        'item_type',
        'material_type',
        'material_grade',
        'thickness_gauge',
        'thickness_mm',
        'dimension',
        'diameter',
        'unit_price',
        'unit_sale_price',
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
            'unit_price' => 'decimal:2',
            'unit_sale_price' => 'decimal:2',
            'thickness_mm' => 'decimal:4',
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

        $itemType = $data['item_type'] ?? '';
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

        if (!empty($data['item_type'])) {
            $parts[] = $data['item_type'];
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
     * Get the category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the folder this product belongs to.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(ProductFolder::class, 'folder_id');
    }

    /**
     * Get the inventory items required as materials for this product.
     */
    public function requiredMaterials(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'product_material')
            ->withPivot('quantity_required', 'notes');
    }

    /**
     * Get the projects that include this product.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_product')
            ->withPivot('quantity', 'unit_price_at_time', 'notes');
    }

    /**
     * How many still-running projects include this product. Those projects
     * have already reserved or consumed stock against its bill of materials,
     * so it must not be deleted while they are open.
     */
    public function activeProjectsCount(): int
    {
        return $this->projects()
            ->whereNotIn('projects.status', InventoryItem::CLOSED_PROJECT_STATUSES)
            ->count();
    }

    /**
     * Get custom field values for this product.
     */
    public function customFieldValues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'entity_id')
            ->where('entity_type', 'product');
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
                'entity_type' => 'product',
                'entity_id' => $this->id,
            ],
            ['value' => $value]
        );
    }
}
