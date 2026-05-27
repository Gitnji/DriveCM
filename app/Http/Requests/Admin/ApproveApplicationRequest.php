<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null; // central admin
    }

    public function rules(): array
    {
        return [
            // D96/D106 — admin may keep or change the proposed subdomain. Same rules as /apply.
            'subdomain' => [
                'required', 'string',
                'min:3', 'max:40',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::notIn(['www', 'admin', 'api', 'app', 'mail', 'ftp', 'root', 'support', 'help']),
                Rule::unique('tenants', 'subdomain')->ignore($this->route('tenant')->id),
            ],
        ];
    }
}