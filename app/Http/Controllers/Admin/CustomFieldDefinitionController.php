<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomFieldDefinitionRequest;
use App\Http\Requests\Admin\UpdateCustomFieldDefinitionRequest;
use App\Models\Category;
use App\Models\CustomFieldDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomFieldDefinitionController extends Controller
{
    /**
     * Display a listing of custom field definitions.
     */
    public function index(Request $request): View
    {
        $entityType = $request->get('entity_type', 'product');

        $definitions = CustomFieldDefinition::with('category')
            ->where('entity_type', $entityType)
            ->orderBy('sort_order')
            ->orderBy('field_label')
            ->paginate(25)
            ->withQueryString();

        return view('admin.custom-field-definitions.index', [
            'definitions' => $definitions,
            'entityType' => $entityType,
            // Inventory fields store the system category key, so the list needs
            // this to print "Metals" rather than the raw "metals".
            'systemCategoryLabels' => \App\Models\DropdownOption::getOptions(\App\Models\DropdownOption::TYPE_SYSTEM_CATEGORY)
                ->pluck('label', 'value')
                ->all(),
        ]);
    }

    /**
     * Show the form for creating a new custom field definition.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        // Inventory items are scoped by system category, not by Category.
        $systemCategories = \App\Models\DropdownOption::getOptions(\App\Models\DropdownOption::TYPE_SYSTEM_CATEGORY);

        return view('admin.custom-field-definitions.create', compact('categories', 'systemCategories'));
    }

    /**
     * Store a newly created custom field definition.
     */
    public function store(StoreCustomFieldDefinitionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        CustomFieldDefinition::create($this->scopeForEntityType($data));

        return redirect()
            ->route('admin.custom-field-definitions.index', ['entity_type' => $data['entity_type']])
            ->with('success', 'Custom field added successfully.');
    }

    /**
     * Show the form for editing the specified custom field definition.
     */
    public function edit(CustomFieldDefinition $customFieldDefinition): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $systemCategories = \App\Models\DropdownOption::getOptions(\App\Models\DropdownOption::TYPE_SYSTEM_CATEGORY);

        return view('admin.custom-field-definitions.edit', [
            'definition' => $customFieldDefinition,
            'categories' => $categories,
            'systemCategories' => $systemCategories,
        ]);
    }

    /**
     * Update the specified custom field definition.
     */
    public function update(UpdateCustomFieldDefinitionRequest $request, CustomFieldDefinition $customFieldDefinition): RedirectResponse
    {
        $data = $request->validated();
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $customFieldDefinition->update($this->scopeForEntityType($data));

        return redirect()
            ->route('admin.custom-field-definitions.index', ['entity_type' => $customFieldDefinition->entity_type])
            ->with('success', 'Custom field updated successfully.');
    }

    /**
     * Keep the scoping columns consistent with the entity type that was chosen.
     *
     * Products are scoped by category, inventory items by system category. Both
     * selectors live on the same form and only one of them is shown at a time,
     * so the hidden one still posts whatever was last picked - which would save
     * a category onto an inventory field, where nothing ever reads it.
     *
     * Unticking every system category posts no key at all, so without the
     * default here validated() would simply omit it and the previous scoping
     * would stay in place, making the field impossible to widen again.
     */
    private function scopeForEntityType(array $data): array
    {
        if (($data['entity_type'] ?? null) === 'inventory_item') {
            $data['category_id'] = null;
            $data['applies_to_item_types'] = array_values(array_filter($data['applies_to_item_types'] ?? [])) ?: null;

            return $data;
        }

        $data['applies_to_item_types'] = null;

        return $data;
    }

    /**
     * Remove the specified custom field definition.
     */
    public function destroy(CustomFieldDefinition $customFieldDefinition): RedirectResponse
    {
        $entityType = $customFieldDefinition->entity_type;
        $customFieldDefinition->delete();

        return redirect()
            ->route('admin.custom-field-definitions.index', ['entity_type' => $entityType])
            ->with('success', 'Custom field deleted successfully.');
    }
}
