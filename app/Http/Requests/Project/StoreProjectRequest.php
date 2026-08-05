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
            'proposal_signed_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'production_deadline' => ['nullable', 'date'],
            'description' => ['nullable'],
            'notes' => ['nullable'],
            'project_manager_id' => ['nullable', 'exists:users,id'],
            'products' => ['nullable', 'array'],
            'products.*.product_id' => ['required_with:products', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'integer', 'min:1'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:teams,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:102400', 'mimes:pdf,png,jpg,jpeg,dwg,dxf,doc,docx,mp4,mov,avi,webm,mkv'], // 100MB max per file, videos included
        ];
    }
}
