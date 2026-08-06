<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DropdownOption extends Model
{
    protected $fillable = [
        'type',
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public const TYPE_ITEM_TYPE = 'item_type';
    public const TYPE_MATERIAL_TYPE = 'material_type';
    public const TYPE_MATERIAL_GRADE = 'material_grade';
    public const TYPE_THICKNESS_GAUGE = 'thickness_gauge';
    public const TYPE_INVENTORY_TYPE = 'inventory_type';
    public const TYPE_SYSTEM_CATEGORY = 'system_category';

    public const TYPES = [
        self::TYPE_ITEM_TYPE => 'Item Type',
        self::TYPE_MATERIAL_TYPE => 'Material Type',
        self::TYPE_MATERIAL_GRADE => 'Material Grade',
        self::TYPE_THICKNESS_GAUGE => 'Thickness (Gauge)',
        self::TYPE_INVENTORY_TYPE => 'Inventory Type',
        self::TYPE_SYSTEM_CATEGORY => 'System Category',
    ];

    /**
     * In-memory copy for the lifetime of this request, so that calling
     * getOptions() multiple times per request (once per type, which every
     * caller does) doesn't repeat the cache lookup. Cache::remember() hits
     * the cache store on every call regardless of the key already being
     * fetched moments ago - with the database cache driver that's a real
     * query every time, so without this a page needing 6 types still ran
     * 6 near-identical "select from cache" queries.
     */
    private static ?\Illuminate\Support\Collection $optionsByType = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCachedOptions());
        static::deleted(fn () => static::forgetCachedOptions());
    }

    private static function forgetCachedOptions(): void
    {
        static::$optionsByType = null;
        Cache::forget('dropdown_options:all');
    }

    /**
     * Every active option, grouped by type, behind a single cache key.
     */
    protected static function allActiveGroupedByType(): \Illuminate\Support\Collection
    {
        return static::$optionsByType ??= Cache::remember('dropdown_options:all', 3600, function () {
            return static::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->groupBy('type');
        });
    }

    public static function getOptions(string $type): \Illuminate\Support\Collection
    {
        return static::allActiveGroupedByType()->get($type, collect());
    }

    public static function getOptionsForSelect(string $type): array
    {
        return static::getOptions($type)
            ->pluck('label', 'value')
            ->toArray();
    }
}
