<?php

namespace App\Http\Requests\Lms;

use App\Rules\BlockContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('author-lessons'); // D5 — owner + instructor
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->content)) {
            // Temporary textarea bridge (D50). Normalise smart/curly quotes to
            // straight quotes before decoding — pasted JSON often arrives with
            // curly quotes (blueprint §3.2 hazard), which json_decode rejects.
            $raw = strtr($this->content, [
                "\u{201C}" => '"', "\u{201D}" => '"',  // curly double quotes
                "\u{2018}" => "'", "\u{2019}" => "'",  // curly single quotes
            ]);

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