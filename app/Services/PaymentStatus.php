<?php

namespace App\Services;

use App\Models\PaymentType;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * P3 (Flow A) — central blocking logic. Single source of truth for "is this student
 * blocked from learning?" Used by LessonProgression::isLessonAccessible(), the
 * dashboard banner, and the my-lessons banner.
 *
 * A student is blocked when:
 *   - They have crossed the trial threshold for at least one REQUIRED + ACTIVE payment type
 *   - AND they don't have an APPROVED StudentPayment for that type
 *
 * Threshold check is strict: the configured level must EXIST in the school's level
 * tree AND be complete for that student.
 */
class PaymentStatus
{
    public function __construct(private LessonProgression $progression) {}

    /**
     * Quick boolean check used by the lesson access gate.
     */
    public function isStudentBlocked(User $student): bool
    {
        return $this->pendingRequiredPayments($student)->isNotEmpty();
    }

    /**
     * Returns the required payment types where this student has crossed the trial
     * threshold but doesn't yet have an approved payment. Drives the student-side
     * Payments page (P4) and the banner copy.
     *
     * @return Collection<PaymentType>
     */
    public function pendingRequiredPayments(User $student): Collection
    {
        // All ACTIVE + REQUIRED types for the tenant.
        $requiredTypes = PaymentType::where('is_required', true)
            ->where('is_active', true)
            ->whereNotNull('levels_required_before_prompt')
            ->get();

        if ($requiredTypes->isEmpty()) {
            return collect();
        }

        // Map type_id -> latest payment status (approved beats anything else).
        $payments = StudentPayment::where('student_id', $student->id)
            ->whereIn('payment_type_id', $requiredTypes->pluck('id'))
            ->get()
            ->groupBy('payment_type_id');

        return $requiredTypes->filter(function (PaymentType $type) use ($student, $payments) {
            // Has the student crossed the threshold for this type?
            if (! $this->studentCrossedThreshold($student, $type->levels_required_before_prompt)) {
                return false;
            }

            // Is there an approved payment?
            $forType = $payments->get($type->id, collect());
            $hasApproved = $forType->contains(fn ($p) => $p->isApproved());

            // If approved → not pending. If not approved → pending (regardless of
            // rejected/pending_review state — the student still needs to pay).
            return ! $hasApproved;
        })->values();
    }

    /**
     * P3 — strict threshold check. The level at the given 0-indexed position must
     * EXIST in the tree AND be in 'complete' state for this student.
     *
     * Different from LessonProgression::hasCompletedFirstLevels which treats
     * "fewer-levels-than-requested" as a pass. For payment gating, we want strict:
     * if the school hasn't configured the prerequisite levels, payment shouldn't
     * auto-prompt.
     *
     * `levelIndex` is 0-based. levelIndex=0 → "after level 1 completed", because
     * the configured value `levels_required_before_prompt=N` means "after N levels
     * completed" — we check that index N-1 is complete, OR phrased differently,
     * that the first N levels are all complete.
     *
     * Actually re-reading the design: levels_required_before_prompt=2 means
     * "prompted after completing level 2". So we need to check that level at
     * position index 1 (zero-indexed, second level) is complete.
     *
     * Simpler: check the first N levels of the tree are all complete AND
     * count >= N. Strict version of hasCompletedFirstLevels.
     */
    public function studentCrossedThreshold(User $student, int $levelsRequired): bool
    {
        if ($levelsRequired <= 0) {
            return true;
        }

        $tree = $this->progression->forStudent($student);

        if (count($tree) < $levelsRequired) {
            return false;
        }

        // P3 — strict: each required level must EXIST, BE COMPLETE, AND HAVE AT LEAST
        // ONE LESSON. Empty levels report state='complete' (per D70) so the practical
        // gate doesn't freeze, but for payment gating an empty level isn't real
        // progress and shouldn't trigger the prompt.
        $firstN = array_slice($tree, 0, $levelsRequired);
        foreach ($firstN as $levelRow) {
            if ($levelRow['state'] !== 'complete') {
                return false;
            }
            if (count($levelRow['lessons']) === 0) {
                return false;
            }
        }
        return true;
    }
}