<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentTypeRequest extends FormRequest
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
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Checkbox shorthand: unchecked = absent from request.
        $this->merge([
            'is_required' => $this->boolean('is_required'),
        ]);
    }

    public function messages(): array
    {
        return [
            'amount_xaf.min' => 'Amount must be at least 1 XAF.',
            'amount_xaf.max' => 'Amount must be at most 50,000,000 XAF.',
        ];
    }
}