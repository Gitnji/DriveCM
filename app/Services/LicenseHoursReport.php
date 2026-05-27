<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PracticalSession;
use App\Models\ReportValidation;
use App\Models\Tenant;
use App\Models\User;

class LicenseHoursReport
{
    /**
     * Build a student's license-hours report data.
     * Returns current hours, the required-hours settings, and the latest validation (if any).
     */
    public function forStudent(User $student): array
    {
        // Theory hours — completed lessons' duration (D40).
        $completedLessonIds = LessonProgress::where('user_id', $student->id)
            ->where('completed', true)
            ->pluck('lesson_id');
        $theoryMinutes = (int) Lesson::whereIn('id', $completedLessonIds)->sum('duration_minutes');

        // Practical hours — completed sessions' duration (D89).
        $practicalMinutes = (int) PracticalSession::where('student_id', $student->id)
            ->where('status', 'completed')
            ->sum('duration_minutes');

        // Per-school required-hours settings (D94 — tenant JSON data, default 0).
        $tenant = Tenant::find(session('tenant_id'));
        $requiredTheory = (int) ($tenant->data['required_theory_minutes'] ?? 0);
        $requiredPractical = (int) ($tenant->data['required_practical_minutes'] ?? 0);

        // Latest validation, if any (D93 — newest row wins).
        $latestValidation = ReportValidation::where('student_id', $student->id)
            ->latest('created_at')
            ->first();

        return [
            'student' => $student,
            'theory_minutes' => $theoryMinutes,
            'practical_minutes' => $practicalMinutes,
            'required_theory_minutes' => $requiredTheory,
            'required_practical_minutes' => $requiredPractical,
            'latest_validation' => $latestValidation,
            // Is the CURRENT report validated, i.e. does a validation exist whose
            // snapshot matches current hours? (D90 — stale validation if hours moved.)
            'is_current_validated' => $latestValidation
                && $latestValidation->theory_minutes === $theoryMinutes
                && $latestValidation->practical_minutes === $practicalMinutes,
        ];
    }
}