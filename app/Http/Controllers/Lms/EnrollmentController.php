<?php

namespace App\Http\Controllers\Lms;

use App\Actions\Tenant\ApproveStudentApplication;
use App\Actions\Tenant\RejectStudentApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RejectStudentApplicationRequest;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $pending = StudentApplication::where('status', 'pending')
            ->orderByDesc('submitted_at')
            ->get();

        $recent = StudentApplication::whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('reviewed_at')
            ->limit(10)
            ->get();

        return view('lms.enrollments.index', [
            'pending' => $pending,
            'recent'  => $recent,
        ]);
    }

    public function show(StudentApplication $application)
    {
        return view('lms.enrollments.show', [
            'application' => $application,
        ]);
    }

    public function approve(StudentApplication $application, ApproveStudentApplication $action)
    {
        abort_unless($application->status === 'pending', 404);

        $result = $action->execute($application, Auth::guard('web')->user());

        return redirect()
            ->route('lms.enrollments.approved', $application)
            ->with('credentials', [
                'email'    => $result['student_email'],
                'password' => $result['temp_password'],
            ]);
    }

    public function approved(StudentApplication $application)
    {
        $credentials = session('credentials');
        if (! $credentials) {
            return redirect()->route('lms.enrollments.index');
        }
        return view('lms.enrollments.approved', [
            'application' => $application,
            'credentials' => $credentials,
        ]);
    }

    public function reject(RejectStudentApplicationRequest $request, StudentApplication $application, RejectStudentApplication $action)
    {
        abort_unless($application->status === 'pending', 404);

        $action->execute(
            $application,
            $request->validated()['rejection_reason'] ?? null,
            Auth::guard('web')->user()
        );

        return redirect()->route('lms.enrollments.index')
            ->with('status', 'Application rejected.');
    }
}