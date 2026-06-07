<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-payments') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'amount_xaf'  => ['required', 'integer', 'min:1', 'max:50000000'],
            'is_required' => ['nullable', 'boolean'],
            'levels_required_before_prompt' => ['nullable', 'integer', 'min:0', 'max:20'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
            'is_active'   => $this->boolean('is_active'),
        ]);
    }
}