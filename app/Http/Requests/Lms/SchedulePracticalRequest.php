<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SchedulePracticalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('schedule-practical');
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where('tenant_id', session('tenant_id'))->where('role', 'student'),
            ],
            'instructor_id' => [
                'required',
                Rule::exists('users', 'id')->where('tenant_id', session('tenant_id'))->where('role', 'instructor'),
            ],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            // D88 — instructor's explicit override checkbox. Optional (absent = not overriding).
            'override_theory' => ['nullable', 'boolean'],
        ];
    }
}