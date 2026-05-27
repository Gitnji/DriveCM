<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only those who author lessons can upload lesson images (D5).
        return Gate::allows('author-lessons');
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'image',                          // must be an actual image
                'mimes:jpeg,png,webp',            // D55 — allowed types
                'max:2048',                       // D55 — 2 MB, in kilobytes
            ],
        ];
    }
}