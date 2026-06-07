<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class RejectStudentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('review-payments') ?? false;
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}