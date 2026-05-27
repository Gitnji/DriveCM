<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MarkPracticalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('schedule-practical'); // instructor + secretary + owner (D82)
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['completed', 'cancelled', 'absent'])],
            // Actual duration — required only when completing (D84). Validated conditionally below.
            'duration_minutes' => ['required_if:status,completed', 'nullable', 'integer', 'min:15', 'max:480'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}