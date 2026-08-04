<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'first_name' => ['required', 'max:191'],
            'last_name' => ['nullable', 'max:191'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'phone_number' => ['nullable', 'regex:/^(\+91[\-\s]?)?[6-9]\d{9}$/'],
            'role_id' => ['required', 'exists:roles,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
        ];
    }
}
