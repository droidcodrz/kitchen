<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomFieldDefinitionRequest extends FormRequest
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

        if ($this->filled('options_text')) {
            $options = array_values(array_filter(array_map('trim', explode("\n", $this->input('options_text')))));
            $this->merge(['options' => $options]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $definitionId = $this->route('custom_field_definition')?->id ?? $this->route('custom_field_definition');

        return [
            'entity_type' => ['required', 'in:product,inventory_item'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'field_name' => [
                'required', 'max:100', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('custom_field_definitions', 'field_name')
                    ->where('entity_type', $this->input('entity_type'))
                    ->ignore($definitionId),
            ],
            'field_label' => ['required', 'max:191'],
            'field_type' => ['required', 'in:text,textarea,number,date,select,boolean,file'],
            'options' => ['nullable', 'array'],
            'options.*' => ['string', 'max:191'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'field_name.regex' => 'Field key may only contain lowercase letters, numbers, and underscores (e.g. handle_style).',
        ];
    }
}
