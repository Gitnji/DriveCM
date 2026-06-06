<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateStudentRequest;
use App\Models\AuditLog;
use App\Models\LessonProgress;
use App\Models\PracticalSession;
use App\Models\ReportValidation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $active = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        $deleted = User::onlyTrashed()
            ->where('role', 'student')
            ->orderByDesc('deleted_at')
            ->get();

        return view('lms.students.index', [
            'active'  => $active,
            'deleted' => $deleted,
        ]);
    }

    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        // Theory progress
        $latestProgress = LessonProgress::where('user_id', $student->id)
            ->with('lesson.level')
            ->latest('updated_at')
            ->first();

        $currentLevel = $latestProgress?->lesson?->level;
        $lessonsCompleted = LessonProgress::where('user_id', $student->id)
            ->where('completed', true)
            ->count();
        $lessonAttempts = LessonProgress::where('user_id', $student->id)
            ->sum('attempt_count');

        // Practical
        $sessions = PracticalSession::where('student_id', $student->id)
            ->with('instructor:id,name')
            ->orderByDesc('scheduled_at')
            ->get();

        $sessionsCompleted = $sessions->where('status', 'completed')->count();
        $sessionsNoShow = $sessions->where('status', 'no_show')->count();
        $practicalMinutes = $sessions->where('status', 'completed')->sum('duration_minutes');

        // Reports
        $reportValidation = ReportValidation::where('student_id', $student->id)
            ->with('validatedBy:id,name')
            ->latest('created_at')
            ->first();

        return view('lms.students.show', [
            'student'           => $student,
            'currentLevel'      => $currentLevel,
            'lessonsCompleted'  => $lessonsCompleted,
            'lessonAttempts'    => $lessonAttempts,
            'sessions'          => $sessions,
            'sessionsCompleted' => $sessionsCompleted,
            'sessionsNoShow'    => $sessionsNoShow,
            'practicalMinutes'  => $practicalMinutes,
            'reportValidation'  => $reportValidation,
        ]);
    }

    public function edit(User $student)
    {
        abort_unless($student->role === 'student', 404);
        return view('lms.students.edit', ['student' => $student]);
    }

    public function update(UpdateStudentRequest $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $student->update($request->validated());

        AuditLog::create([
            'tenant_id'    => $student->tenant_id,
            'actor_type'   => 'user',
            'actor_id'     => (string) Auth::guard('web')->id(),
            'action'       => 'student.updated',
            'subject_type' => 'user',
            'subject_id'   => (string) $student->id,
            'detail'       => ['changes' => $request->validated()],
        ]);

        return redirect()->route('lms.students.show', $student)
            ->with('status', 'Student updated.');
    }

    public function destroy(User $student)
    {
        abort_unless($student->role === 'student', 404);

        DB::transaction(function () use ($student) {
            $student->delete(); // soft delete

            AuditLog::create([
                'tenant_id'    => $student->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'student.removed',
                'subject_type' => 'user',
                'subject_id'   => (string) $student->id,
                'detail'       => [],
            ]);
        });

        return redirect()->route('lms.students.index')
            ->with('status', 'Student removed.');
    }

    public function restore(int $id)
    {
        $student = User::withTrashed()->findOrFail($id);
        abort_unless($student->role === 'student', 404);

        DB::transaction(function () use ($student) {
            $student->restore();

            AuditLog::create([
                'tenant_id'    => $student->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'student.restored',
                'subject_type' => 'user',
                'subject_id'   => (string) $student->id,
                'detail'       => [],
            ]);
        });

        return redirect()->route('lms.students.index')
            ->with('status', 'Student restored.');
    }
}