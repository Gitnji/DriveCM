<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectStudentApplication
{
    public function execute(StudentApplication $application, ?string $reason, User $rejectedBy): void
    {
        if ($application->status !== 'pending') {
            throw new \LogicException('Only a pending application can be rejected.');
        }

        DB::transaction(function () use ($application, $reason, $rejectedBy) {
            $application->update([
                'status'           => 'rejected',
                'reviewed_at'      => now(),
                'reviewed_by'      => $rejectedBy->id,
                'rejection_reason' => $reason,
            ]);

            AuditLog::create([
                'tenant_id'    => $application->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $rejectedBy->id,
                'action'       => 'student.application.rejected',
                'subject_type' => 'student_application',
                'subject_id'   => (string) $application->id,
                'detail'       => ['reason' => $reason],
            ]);
        });
    }
}