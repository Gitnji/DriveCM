<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-payments') ?? false;
    }

    public function rules(): array
    {
        return [
            'momo_number'          => ['nullable', 'string', 'max:40'],
            'orange_number'        => ['nullable', 'string', 'max:40'],
            'payment_instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }
}