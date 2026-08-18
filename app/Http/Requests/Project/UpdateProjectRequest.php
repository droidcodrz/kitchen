<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
        $projectId = $this->route('project')?->id ?? $this->route('project');

        return [
            'name' => ['required', 'max:191', 'unique:projects,name,' . $projectId],
            'client_id' => ['required', 'exists:clients,id'],
            // The status <select> only ever offers draft/confirmed, but once a
            // project has moved past that (design, in_production, delayed, ...)
            // the form falls back to a read-only display that still posts the
            // project's current status via a hidden field, so this must accept
            // every valid status or every edit to an in-progress project fails.
            'status' => ['required', 'in:draft,confirmed,design,in_production,delayed,inspection,finished,delivered'],
            'project_manager_id' => ['nullable', 'exists:users,id'],
            'proposal_signed_date' => ['nullable', 'date', function ($attribute, $value, $fail) {
                $delivery = $this->input('delivery_date');
                if ($delivery && \Carbon\Carbon::parse($value)->gt(\Carbon\Carbon::parse($delivery))) {
                    $fail('The proposal date must not be after the delivery date.');
                }
            }],
            'delivery_date' => ['nullable', 'date'],
            'production_deadline' => ['nullable', 'date'],
            'description' => ['nullable'],
            'notes' => ['nullable'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['string', 'max:50'],
            'products' => ['nullable', 'array'],
            // Both project forms always render one blank equipment row by
            // default (product_id empty, quantity 1) so there's a row to
            // fill in - that blank row must stay valid on its own, since
            // ProjectService already skips rows with no product_id.
            'products.*.product_id' => ['nullable', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'integer', 'min:1'],
            'inventory_items' => ['nullable', 'array'],
            'inventory_items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'inventory_items.*.quantity' => ['required_with:inventory_items', 'numeric', 'min:0.01'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:teams,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['exists:attachments,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:102400', 'mimes:pdf,png,jpg,jpeg,dwg,dxf,doc,docx,mp4,mov,avi,webm,mkv'], // 100MB max per file, videos included
        ];
    }

    /**
     * A project needs at least one real thing on it - either a manufactured
     * Product or a directly-attached Inventory Item (or both). The form
     * always renders one blank row of each by default, so it's easy to
     * submit with neither ever actually filled in - block that rather than
     * silently leaving a project with no equipment at all.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasProduct = collect($this->input('products', []))
                ->contains(fn ($row) => !empty($row['product_id'] ?? null));

            $hasInventoryItem = collect($this->input('inventory_items', []))
                ->contains(fn ($row) => !empty($row['inventory_item_id'] ?? null));

            if (!$hasProduct && !$hasInventoryItem) {
                $validator->errors()->add('products', 'Select at least one product or inventory item for this project.');
            }
        });
    }
}
