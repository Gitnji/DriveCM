<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectStudentPayment
{
    public function execute(StudentPayment $payment, User $reviewer, ?string $reason): void
    {
        if ($payment->status !== 'pending_review') {
            throw new \LogicException('Only a pending payment can be rejected.');
        }

        DB::transaction(function () use ($payment, $reviewer, $reason) {
            $payment->update([
                'status'           => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_at'      => now(),
                'reviewed_by'      => $reviewer->id,
            ]);

            AuditLog::create([
                'tenant_id'    => $payment->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $reviewer->id,
                'action'       => 'student_payment.rejected',
                'subject_type' => 'student_payment',
                'subject_id'   => (string) $payment->id,
                'detail'       => ['reason' => $reason],
            ]);
        });
    }
}