<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuestionPayload implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The question is malformed.');
            return;
        }

        $type = $value['type'] ?? null;
        if (! in_array($type, ['mcq', 'true_false'], true)) {
            $fail('Invalid question type.');
            return;
        }

        $prompt = $value['prompt'] ?? null;
        if (! is_string($prompt) || trim($prompt) === '') {
            $fail('The question prompt is required.');
            return;
        }

        $options = $value['options'] ?? null;
        if (! is_array($options)) {
            $fail('The question must have options.');
            return;
        }

        $count = count($options);

        if ($type === 'true_false' && $count !== 2) {
            $fail('A true/false question must have exactly 2 options.');
            return;
        }
        if ($type === 'mcq' && ($count < 2 || $count > 6)) {
            $fail('A multiple-choice question must have between 2 and 6 options.');
            return;
        }

        $correctCount = 0;
        foreach ($options as $i => $opt) {
            $n = $i + 1;
            if (! is_array($opt)) {
                $fail("Option {$n} is malformed.");
                return;
            }
            if (! isset($opt['text']) || ! is_string($opt['text']) || trim($opt['text']) === '') {
                $fail("Option {$n} text is required.");
                return;
            }
            if (! array_key_exists('is_correct', $opt) || ! is_bool($opt['is_correct'])) {
                $fail("Option {$n} is missing a valid correct/incorrect flag.");
                return;
            }
            if ($opt['is_correct'] === true) {
                $correctCount++;
            }
        }

        if ($correctCount !== 1) {
            $fail('Exactly one option must be marked correct.');
            return;
        }
    }
}