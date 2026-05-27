<?php

namespace App\Http\Requests\Lms;

use App\Rules\QuestionPayload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('author-lessons'); // D5
    }

    protected function prepareForValidation(): void
    {
        // Question payload arrives as JSON in a hidden field (D66).
        // Use input(), NOT the magic property (content-bug lesson).
        $raw = $this->input('question');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $this->merge([
                'question' => is_array($decoded) ? $decoded : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'array', new QuestionPayload()],
        ];
    }
}