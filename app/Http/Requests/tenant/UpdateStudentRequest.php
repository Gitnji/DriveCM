<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-students') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:120'],
            'phone'    => ['nullable', 'string', 'max:40'],
            'town'     => ['required', 'string', 'max:120'],
            'language' => ['required', Rule::in(['en', 'fr'])],
            // Email NOT editable per D169.
        ];
    }
}