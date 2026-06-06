<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-staff') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:120'],
            // Unique per tenant, ignoring soft-deleted (matches the partial unique index).
            // BelongsToTenant scopes User::where, so the implicit tenant filter handles tenancy.
            'email' => [
                'required', 'string', 'email:rfc,dns', 'max:160',
                Rule::unique('users', 'email')
                    ->where('tenant_id', $this->user()->tenant_id)
                    ->whereNull('deleted_at'),
            ],
            'role'  => ['required', Rule::in(['secretary', 'instructor'])],
            'language' => ['required', Rule::in(['en', 'fr'])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A staff member with this email already exists at your school.',
            'role.in'      => 'Role must be secretary or instructor.',
        ];
    }
}