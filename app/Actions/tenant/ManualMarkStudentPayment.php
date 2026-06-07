<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\PaymentType;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ManualMarkStudentPayment
{
    /**
     * P5 — owner/secretary marks a student as paid in cash (or otherwise outside
     * the screenshot flow). Creates an approved StudentPayment directly with
     * created_via='manual_mark' for audit clarity.
     */
    public function execute(User $student, PaymentType $type, User $reviewer, ?string $notes): StudentPayment
    {
        if (! $student->isStudent()) {
            throw new \LogicException('Manual mark only applies to student users.');
        }
        if ($student->tenant_id !== $type->tenant_id || $student->tenant_id !== $reviewer->tenant_id) {
            throw new \LogicException('Cross-tenant manual mark is not allowed.');
        }

        return DB::transaction(function () use ($student, $type, $reviewer, $notes) {
            $payment = StudentPayment::create([
                'tenant_id'       => $student->tenant_id,
                'student_id'      => $student->id,
                'payment_type_id' => $type->id,
                'status'          => 'approved',
                'amount_xaf'      => $type->amount_xaf,
                'notes'           => $notes,
                'created_via'     => 'manual_mark',
                'submitted_at'    => now(),
                'reviewed_at'     => now(),
                'reviewed_by'     => $reviewer->id,
            ]);

            AuditLog::create([
                'tenant_id'    => $reviewer->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $reviewer->id,
                'action'       => 'student_payment.manual_marked',
                'subject_type' => 'student_payment',
                'subject_id'   => (string) $payment->id,
                'detail'       => [
                    'student_id'      => $student->id,
                    'payment_type_id' => $type->id,
                    'amount_xaf'      => $type->amount_xaf,
                ],
            ]);

            return $payment;
        });
    }
}