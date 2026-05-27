<?php

namespace App\Http\Requests\Lms;

use App\Rules\BlockContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('author-lessons');
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('content');

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $this->merge([
                'content' => is_array($decoded) ? $decoded : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'level_id' => [
                'required',
                Rule::exists('levels', 'id')->where('tenant_id', session('tenant_id')),
            ],
            'title' => ['required', 'string', 'max:200'],
            'position' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'pass_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:1000'],
            'content' => ['array', new BlockContent()],
        ];
    }
}