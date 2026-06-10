<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route is already gated by auth:admin; admins are platform staff.
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'monthly_fee_xaf'      => ['required', 'integer', 'min:0', 'max:10000000'],
            'free_trial_days'      => ['required', 'integer', 'min:0', 'max:365'],
            'momo_number'          => ['nullable', 'string', 'max:40'],
            'orange_number'        => ['nullable', 'string', 'max:40'],
            'payment_instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'monthly_fee_xaf.min' => 'Monthly fee cannot be negative.',
            'monthly_fee_xaf.max' => 'Monthly fee seems too high — please check.',
            'free_trial_days.max' => 'Free trial cannot exceed one year.',
        ];
    }
}