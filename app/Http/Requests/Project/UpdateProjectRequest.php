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
            'proposal_signed_date' => ['nullable', 'date'],
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
}
