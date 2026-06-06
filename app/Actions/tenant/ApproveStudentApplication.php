<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApproveStudentApplication
{
    public function execute(StudentApplication $application, User $approvedBy): array
    {
        if ($application->status !== 'pending') {
            throw new \LogicException('Only a pending application can be approved.');
        }

        $tempPassword = Str::random(14);

        return DB::transaction(function () use ($application, $approvedBy, $tempPassword) {
            // 1) Create student user (D98 — temp password shown once).
            $student = User::create([
                'tenant_id' => $application->tenant_id,
                'name'      => $application->name,
                'email'     => $application->email,
                'password'  => $tempPassword, // hashed via User::$casts
                'role'      => 'student',
                'language'  => 'en',
                'must_change_password' => true,
            ]);

            // 2) Mark application approved.
            $application->update([
                'status'      => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $approvedBy->id,
            ]);

            // 3) Audit log.
            AuditLog::create([
                'tenant_id'    => $application->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $approvedBy->id,
                'action'       => 'student.enrolled',
                'subject_type' => 'student_application',
                'subject_id'   => (string) $application->id,
                'detail'       => ['user_id' => $student->id],
            ]);

            return [
                'application'   => $application->fresh(),
                'student'       => $student,
                'student_email' => $student->email,
                'temp_password' => $tempPassword,
            ];
        });
    }
}