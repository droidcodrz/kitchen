<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'name' => ['nullable', 'max:191', 'unique:products,name,' . $productId],
            'name_suffix' => ['nullable', 'max:100'],
            'sku' => ['nullable', 'unique:products,sku,' . $productId, 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'folder_id' => ['nullable', 'exists:product_folders,id'],
            'type' => ['nullable', 'max:100'],
            'inventory_type' => ['nullable', 'max:100'],
            'item_type' => ['nullable', 'max:100'],
            // Optional, matching item_type above and how the records actually
            // are: no existing product carries a material type, so requiring
            // it made every one of them impossible to save from the edit
            // form. Label generation already handles its absence.
            'material_type' => ['nullable', 'max:100'],
            'material_grade' => ['nullable', 'max:50'],
            'thickness_gauge' => ['nullable', 'max:50'],
            'thickness_mm' => ['nullable', 'numeric', 'min:0'],
            'dimension' => ['nullable', 'max:50'],
            'diameter' => ['nullable', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'unit_sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable'],
            'material_ids' => ['nullable', 'array'],
            'material_ids.*' => ['exists:inventory_items,id'],
            'material_quantities' => ['nullable', 'array'],
            'material_quantities.*' => ['nullable', 'numeric', 'min:0.01', 'max:999999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists. Please use a different name, or add a suffix to tell them apart.',
            'sku.unique' => 'A product with this SKU already exists.',
            'material_quantities.*.max' => 'Material quantity cannot be more than 999999.',
            'material_quantities.*.min' => 'Material quantity must be greater than 0.',
        ];
    }
}
