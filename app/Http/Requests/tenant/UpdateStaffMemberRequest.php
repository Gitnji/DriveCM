<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-staff') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:120'],
            'role'     => ['required', Rule::in(['secretary', 'instructor'])],
            'language' => ['required', Rule::in(['en', 'fr'])],
            // Email NOT editable per D168 — omitted from rules.
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role must be secretary or instructor.',
        ];
    }
}