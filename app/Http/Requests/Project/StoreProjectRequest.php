<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
        return [
            'name' => ['required', 'max:191', 'unique:projects,name'],
            'client_id' => ['required', 'exists:clients,id'],
            'status' => ['required', 'in:draft,confirmed'],
            // before_or_equal:delivery_date isn't used here - it fails validation
            // outright whenever delivery_date is left blank at all (both dates are
            // legitimately optional, set independently), not just when it's actually
            // before the proposal date. Only compare when both are present.
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
            'project_manager_id' => ['nullable', 'exists:users,id'],
            'products' => ['nullable', 'array'],
            // Both project forms always render one blank equipment row by
            // default (product_id empty, quantity 1) so there's a row to
            // fill in - that blank row must stay valid on its own, since
            // ProjectService already skips rows with no product_id.
            'products.*.product_id' => ['nullable', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'integer', 'min:1'],
            'inventory_items' => ['nullable', 'array'],
            // Same pattern as products above - the form always renders one
            // blank row by default, so the empty row must stay valid.
            'inventory_items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'inventory_items.*.quantity' => ['required_with:inventory_items', 'numeric', 'min:0.01'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:teams,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:102400', 'mimes:pdf,png,jpg,jpeg,dwg,dxf,doc,docx,mp4,mov,avi,webm,mkv'], // 100MB max per file, videos included
        ];
    }
}
