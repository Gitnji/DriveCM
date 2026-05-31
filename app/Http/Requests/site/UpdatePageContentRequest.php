<?php

namespace App\Http\Requests\Site;

use App\Rules\PageBlockContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-site');
    }

    /**
     * The form posts `content` as a JSON string (from page-editor.js). Decode it BEFORE validation
     * so the rule sees an array. NEVER use $this->content magic property here — same content-bug
     * lesson as the lessons editor.
     */
    protected function prepareForValidation(): void
    {
        $raw = $this->input('content');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $this->merge(['content' => is_array($decoded) ? $decoded : []]);
        }
    }

    public function rules(): array
    {
        return [
            'content' => ['present', 'array', new PageBlockContent()],
        ];
    }
}