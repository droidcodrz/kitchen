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
        ]);
    }

    /**
     * Show the form for creating a new custom field definition.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.custom-field-definitions.create', compact('categories'));
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

        CustomFieldDefinition::create($data);

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

        return view('admin.custom-field-definitions.edit', [
            'definition' => $customFieldDefinition,
            'categories' => $categories,
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

        $customFieldDefinition->update($data);

        return redirect()
            ->route('admin.custom-field-definitions.index', ['entity_type' => $customFieldDefinition->entity_type])
            ->with('success', 'Custom field updated successfully.');
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