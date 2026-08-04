<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(array_map(
            fn ($value) => is_string($value) ? strip_tags($value) : $value,
            $this->all()
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $itemId = $this->route('inventory_item')?->id ?? $this->route('inventory_item');

        return [
            'name' => ['nullable', 'max:191'],
            'name_suffix' => ['nullable', 'max:100'],
            'sku' => ['nullable', 'unique:inventory_items,sku,' . $itemId, 'max:100'],
            'item_type' => ['required', 'max:100'],
            'item_type_label' => ['nullable', 'max:100'],
            'inventory_type' => ['nullable', 'max:100'],
            'material_type' => ['required', 'max:100'],
            'material_grade' => ['nullable', 'max:50'],
            'thickness_gauge' => ['nullable', 'max:50'],
            'thickness_mm' => ['nullable', 'numeric', 'min:0'],
            'dimension' => ['nullable', 'max:50'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'reserved_quantity' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock_level' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'unit_sale_price' => ['nullable', 'numeric', 'min:0'],
            'unit_of_measure' => ['nullable', 'max:50'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'storage_location_id' => ['nullable', 'exists:storage_locations,id'],
            'last_added_quantity' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}