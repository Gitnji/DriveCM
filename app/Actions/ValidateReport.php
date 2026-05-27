<?php

namespace App\Actions;

use App\Models\AuditLog;
use App\Models\ReportValidation;
use App\Models\User;
use App\Services\LicenseHoursReport;

class ValidateReport
{
    public function __construct(private LicenseHoursReport $report) {}

    /**
     * Validate a student's current report (D12, D90, D93).
     * Snapshots current hours into a new report_validations row; audit-logged.
     */
    public function execute(User $student, User $validatedBy): ReportValidation
    {
        $data = $this->report->forStudent($student);

        $validation = ReportValidation::create([
            'student_id' => $student->id,
            'validated_by' => $validatedBy->id,
            'theory_minutes' => $data['theory_minutes'],
            'practical_minutes' => $data['practical_minutes'],
        ]);

        AuditLog::create([
            'tenant_id' => session('tenant_id'),
            'actor_type' => 'user',
            'actor_id' => $validatedBy->id,
            'action' => 'report.validated',
            'subject_type' => 'report_validation',
            'subject_id' => $validation->id,
            'detail' => [
                'student_id' => $student->id,
                'theory_minutes' => $data['theory_minutes'],
                'practical_minutes' => $data['practical_minutes'],
            ],
        ]);

        return $validation;
    }
}