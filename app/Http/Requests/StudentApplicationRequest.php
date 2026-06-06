<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public form on a tenant subdomain — anyone can submit.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:120'],
            // D165 — email:rfc,dns catches typos and nonexistent domains. Doesn't catch
            // "valid domain with no real inbox" — that requires verification email,
            // which depends on email infrastructure (deferred).
            'email' => ['required', 'string', 'email:rfc,dns', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'town'  => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],

            // Honeypot. Must be empty. Bots dumbly fill every input; real users never see it.
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address that we can reach you at.',
            'phone.required' => 'A phone number is needed — the school will call you to confirm.',
            'website.max'  => 'Your submission could not be processed.', // Honeypot rejection.
        ];
    }
}