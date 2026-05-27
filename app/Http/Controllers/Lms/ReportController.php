<?php

namespace App\Http\Controllers\Lms;

use App\Actions\ValidateReport;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LicenseHoursReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(LicenseHoursReport $report)
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        $rows = $students->map(fn ($student) => $report->forStudent($student));

        return view('lms.reports.index', ['rows' => $rows]);
    }

    public function validate(User $student, ValidateReport $validator)
    {
        abort_unless($student->isStudent(), 404);

        $validator->execute($student, Auth::guard('web')->user());

        return redirect()
            ->route('lms.reports.index')
            ->with('status', __('Report validated for :name.', ['name' => $student->name]));
    }

    public function export(User $student, LicenseHoursReport $report)
    {
        abort_unless($student->isStudent(), 404);

        $data = $report->forStudent($student);

        // D91 — export is LOCKED unless the report is currently validated
        // (a validation exists AND its snapshot matches current hours, D90).
        abort_unless($data['is_current_validated'], 403, 'Report must be validated before export.');

        $validation = $data['latest_validation'];
        $tenant = Tenant::find(session('tenant_id'));

        $pdf = Pdf::loadView('lms.reports.pdf', [
            'tenantName' => $tenant->name,
            'student' => $student,
            'theoryMinutes' => $data['theory_minutes'],
            'practicalMinutes' => $data['practical_minutes'],
            'requiredTheory' => $data['required_theory_minutes'],
            'requiredPractical' => $data['required_practical_minutes'],
            'validatedByName' => $validation->validatedBy->name,
            'validatedAt' => $validation->created_at->format('j M Y'),
            'validationId' => $validation->id,
            'generatedAt' => now()->format('j M Y H:i'),
        ]);

        $filename = 'license-hours-' . str()->slug($student->name) . '.pdf';

        return $pdf->download($filename);
    }
}