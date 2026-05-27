<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        // D46 — owner + instructor. Authorization lives in the Form Request.
        return Gate::allows('manage-levels');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}