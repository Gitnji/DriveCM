<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:120'],
            'phone'    => ['nullable', 'string', 'max:40'],
            'town'     => ['nullable', 'string', 'max:120'],
            'language' => ['required', Rule::in(['en', 'fr'])],
            // Email NOT editable — auth identity.
        ];
    }
}