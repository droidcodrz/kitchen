<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingRequest extends FormRequest
{
    /**
     * The name and logo are what every user sees on the login screen and in
     * the sidebar, so changing them is an administrator's job rather than
     * something each user can do to the whole installation.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->role !== null
            && $user->role->slug === 'admin';
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('app_name')) {
            $this->merge(['app_name' => trim(strip_tags((string) $this->input('app_name')))]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Nullable rather than required: clearing it is how the default
            // name is restored. The limit keeps it inside the sidebar and the
            // browser tab rather than overflowing them.
            'app_name' => ['nullable', 'string', 'max:60'],

            // 'image' on top of the extension list so a renamed file that is
            // not really an image is rejected, and svg is left out of both
            // deliberately: it can carry script, and it is rendered inline on
            // a page every user loads.
            'logo' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],

            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a PNG, JPG or WEBP file.',
            'logo.max' => 'The logo may not be larger than 2 MB.',
            'logo.dimensions' => 'The logo may not be wider or taller than 2000 pixels.',
            'app_name.max' => 'The application name may not be longer than 60 characters.',
        ];
    }
}
