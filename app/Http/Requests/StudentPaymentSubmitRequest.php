<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentPaymentSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            'payment_type_id' => ['required', 'integer', 'exists:payment_types,id'],
            'screenshot'      => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'screenshot.required' => 'Please attach a screenshot of your payment.',
            'screenshot.image'    => 'The file must be an image.',
            'screenshot.mimes'    => 'The screenshot must be JPEG, PNG, or WebP.',
            'screenshot.max'      => 'The screenshot must be 2MB or smaller.',
        ];
    }
}