<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-site');
    }

    public function rules(): array
    {
        return [
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo_upload_id' => ['nullable', 'integer', 'exists:uploads,id'],
            'footer_show_email' => ['nullable', 'boolean'],
            'footer_show_phone' => ['nullable', 'boolean'],
        ];
    }
}