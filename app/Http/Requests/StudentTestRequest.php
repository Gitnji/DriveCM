<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            // answers is [question_id => option_id]; both are integer-ish strings.
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer'],
        ];
    }
}