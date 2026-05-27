<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyForSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public form
    }

    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:120'],
            'desired_subdomain' => [
                'required', 'string',
                'min:3', 'max:40',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::notIn(['www', 'admin', 'api', 'app', 'mail', 'ftp', 'root', 'support', 'help']),
                Rule::unique('tenants', 'subdomain'),
                Rule::unique('tenants', 'desired_subdomain'),
            ],
            'contact_name' => ['required', 'string', 'max:120'],
            'contact_email' => ['required', 'email', 'max:160'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'applicant_town' => ['required', 'string', 'max:80'],

            // D101 honeypot
            'website' => ['nullable', 'size:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'desired_subdomain.regex' => 'The subdomain may use lowercase letters, numbers and single hyphens.',
            'desired_subdomain.not_in' => 'That subdomain is reserved. Please choose another.',
            'desired_subdomain.unique' => 'That subdomain is already taken.',
            'website.size' => 'Spam detected.',
        ];
    }
}