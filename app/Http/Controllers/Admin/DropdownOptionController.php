<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DropdownOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DropdownOptionController extends Controller
{
    public function index(Request $request): View
    {
        $currentType = $request->get('type', DropdownOption::TYPE_ITEM_TYPE);

        $options = DropdownOption::where('type', $currentType)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(25)
            ->withQueryString();

        return view('admin.dropdown-options.index', [
            'options' => $options,
            'types' => DropdownOption::TYPES,
            'currentType' => $currentType,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', array_keys(DropdownOption::TYPES))],
            'value' => ['required', 'max:191'],
            'label' => ['required', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated = array_map(
            fn ($v) => is_string($v) ? strip_tags($v) : $v,
            $validated
        );

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if (DropdownOption::where('type', $validated['type'])->where('value', $validated['value'])->exists()) {
            return redirect()
                ->route('admin.dropdown-options.index', ['type' => $validated['type']])
                ->with('error', 'This value already exists for the selected type.');
        }

        DropdownOption::create($validated);

        return redirect()
            ->route('admin.dropdown-options.index', ['type' => $validated['type']])
            ->with('success', 'Option added successfully.');
    }

    public function update(Request $request, DropdownOption $dropdownOption): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'max:191'],
            'label' => ['required', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated = array_map(
            fn ($v) => is_string($v) ? strip_tags($v) : $v,
            $validated
        );

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $duplicate = DropdownOption::where('type', $dropdownOption->type)
            ->where('value', $validated['value'])
            ->where('id', '!=', $dropdownOption->id)
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('admin.dropdown-options.index', ['type' => $dropdownOption->type])
                ->with('error', 'This value already exists.');
        }

        $dropdownOption->update($validated);

        return redirect()
            ->route('admin.dropdown-options.index', ['type' => $dropdownOption->type])
            ->with('success', 'Option updated successfully.');
    }

    public function destroy(DropdownOption $dropdownOption): RedirectResponse
    {
        $type = $dropdownOption->type;
        $dropdownOption->delete();

        return redirect()
            ->route('admin.dropdown-options.index', ['type' => $type])
            ->with('success', 'Option deleted successfully.');
    }
}
