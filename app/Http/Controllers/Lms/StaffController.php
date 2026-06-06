<?php

namespace App\Http\Controllers\Lms;

use App\Actions\Tenant\CreateStaffMember;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreStaffMemberRequest;
use App\Http\Requests\Tenant\UpdateStaffMemberRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $active = User::whereIn('role', ['secretary', 'instructor'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $deleted = User::onlyTrashed()
            ->whereIn('role', ['secretary', 'instructor'])
            ->orderByDesc('deleted_at')
            ->get();

        return view('lms.staff.index', [
            'active'  => $active,
            'deleted' => $deleted,
        ]);
    }

    public function create()
    {
        return view('lms.staff.create');
    }

    public function store(StoreStaffMemberRequest $request, CreateStaffMember $action)
    {
        $result = $action->execute($request->validated(), Auth::guard('web')->user());

        return redirect()
            ->route('lms.staff.created', $result['staff'])
            ->with('credentials', [
                'email'    => $result['staff_email'],
                'password' => $result['temp_password'],
            ]);
    }

    public function created(User $staff)
    {
        // Ownership: must be a staff member in this tenant (BelongsToTenant covers it).
        abort_unless(in_array($staff->role, ['secretary', 'instructor'], true), 404);

        $credentials = session('credentials');
        if (! $credentials) {
            return redirect()->route('lms.staff.index');
        }

        return view('lms.staff.created', [
            'staff'       => $staff,
            'credentials' => $credentials,
        ]);
    }

    public function edit(User $staff)
    {
        abort_unless(in_array($staff->role, ['secretary', 'instructor'], true), 404);

        return view('lms.staff.edit', ['staff' => $staff]);
    }

    public function update(UpdateStaffMemberRequest $request, User $staff)
    {
        abort_unless(in_array($staff->role, ['secretary', 'instructor'], true), 404);

        $staff->update($request->validated());

        AuditLog::create([
            'tenant_id'    => $staff->tenant_id,
            'actor_type'   => 'user',
            'actor_id'     => (string) Auth::guard('web')->id(),
            'action'       => 'staff.updated',
            'subject_type' => 'user',
            'subject_id'   => (string) $staff->id,
            'detail'       => ['changes' => $request->validated()],
        ]);

        return redirect()->route('lms.staff.index')->with('status', 'Staff member updated.');
    }

    public function destroy(User $staff)
    {
        abort_unless(in_array($staff->role, ['secretary', 'instructor'], true), 404);
        abort_if($staff->id === Auth::guard('web')->id(), 403); // can't soft-delete yourself

        DB::transaction(function () use ($staff) {
            $staff->delete(); // soft delete via SoftDeletes trait

            AuditLog::create([
                'tenant_id'    => $staff->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'staff.removed',
                'subject_type' => 'user',
                'subject_id'   => (string) $staff->id,
                'detail'       => ['role' => $staff->role],
            ]);
        });

        return redirect()->route('lms.staff.index')->with('status', 'Staff member removed.');
    }

    public function restore(int $id)
    {
        $staff = User::withTrashed()->findOrFail($id);
        abort_unless(in_array($staff->role, ['secretary', 'instructor'], true), 404);

        DB::transaction(function () use ($staff) {
            $staff->restore();

            AuditLog::create([
                'tenant_id'    => $staff->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'staff.restored',
                'subject_type' => 'user',
                'subject_id'   => (string) $staff->id,
                'detail'       => ['role' => $staff->role],
            ]);
        });

        return redirect()->route('lms.staff.index')->with('status', 'Staff member restored.');
    }
}