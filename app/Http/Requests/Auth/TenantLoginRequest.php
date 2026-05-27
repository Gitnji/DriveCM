<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TenantLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}