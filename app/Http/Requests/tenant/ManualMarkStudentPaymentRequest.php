<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ManualMarkStudentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('review-payments') ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id'      => ['required', 'integer', 'exists:users,id'],
            'payment_type_id' => ['required', 'integer', 'exists:payment_types,id'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ];
    }
}