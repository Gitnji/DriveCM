<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\MarkPracticalRequest;
use App\Http\Requests\Lms\SchedulePracticalRequest;
use App\Models\AuditLog;
use App\Models\PracticalSession;
use App\Models\User;
use App\Services\LessonProgression;
use Illuminate\Support\Facades\Auth;

class PracticalSessionController extends Controller
{
    public function index()
    {
        $sessions = PracticalSession::with(['student', 'instructor'])
            ->orderByDesc('scheduled_at')
            ->get();

        return view('lms.practical.index', ['sessions' => $sessions]);
    }

    public function create()
    {
        return view('lms.practical.create', [
            'students' => User::where('role', 'student')->orderBy('name')->get(),
            'instructors' => User::where('role', 'instructor')->orderBy('name')->get(),
        ]);
    }

    public function store(SchedulePracticalRequest $request, LessonProgression $progression)
    {
        $actor = Auth::guard('web')->user();
        $data = $request->validated();

        $student = User::where('role', 'student')->findOrFail($data['student_id']);

        $gateMet = $progression->hasCompletedFirstLevels($student, 2);
        $wantsOverride = (bool) ($data['override_theory'] ?? false);
        $overridden = false;

        if (! $gateMet) {
            // D85/D88 — gate not met. Only an instructor, ticking the override box, may proceed.
            if (! $actor->isInstructor() || ! $wantsOverride) {
                return back()
                    ->withInput()
                    ->withErrors(['student_id' => __('This student must complete Levels 1 and 2. An instructor may override.')]);
            }
            $overridden = true;
        }

        $session = PracticalSession::create([
            'student_id' => $student->id,
            'instructor_id' => $data['instructor_id'],
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $data['duration_minutes'],
            'status' => 'scheduled',
            'theory_gate_overridden' => $overridden,
        ]);

        if ($overridden) {
            AuditLog::create([
                'tenant_id' => session('tenant_id'),
                'actor_type' => 'user',
                'actor_id' => $actor->id,
                'action' => 'practical.theory_gate_override',
                'subject_type' => 'practical_session',
                'subject_id' => $session->id,
                'detail' => ['student_id' => $student->id],
            ]);
        }

        return redirect()->route('lms.practical.index')
            ->with('status', __('Practical session scheduled.'));
    }

    public function mark(MarkPracticalRequest $request, PracticalSession $session)
    {
        // Trait scopes the route-model binding — a foreign session 404s.
        $actor = Auth::guard('web')->user();
        $data = $request->validated();

        $session->status = $data['status'];
        $session->notes = $data['notes'] ?? $session->notes;

        if ($data['status'] === 'completed') {
            // D84 — actual duration, adjustable at marking.
            $session->duration_minutes = $data['duration_minutes'];
            $session->completed_at = now();
        } else {
            $session->completed_at = null; // cancelled / absent — not a completion
        }

        $session->marked_by = $actor->id;
        $session->save();

        return redirect()->route('lms.practical.index')
            ->with('status', __('Session updated.'));
    }
}