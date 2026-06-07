<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApproveStudentPayment
{
    public function execute(StudentPayment $payment, User $reviewer): void
    {
        if ($payment->status !== 'pending_review') {
            throw new \LogicException('Only a pending payment can be approved.');
        }

        DB::transaction(function () use ($payment, $reviewer) {
            $payment->update([
                'status'      => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
            ]);

            AuditLog::create([
                'tenant_id'    => $payment->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $reviewer->id,
                'action'       => 'student_payment.approved',
                'subject_type' => 'student_payment',
                'subject_id'   => (string) $payment->id,
                'detail'       => [
                    'student_id'      => $payment->student_id,
                    'payment_type_id' => $payment->payment_type_id,
                    'amount_xaf'      => $payment->amount_xaf,
                ],
            ]);
        });
    }
}