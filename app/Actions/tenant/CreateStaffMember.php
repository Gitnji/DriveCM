<?php

namespace App\Actions\Tenant;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateStaffMember
{
    /**
     * STAFF (D168) — owner creates a secretary or instructor. Transactional create + one-shot
     * temp password (mirrors REG-2 ApproveApplication and ENROLL-3 ApproveStudent).
     *
     * @param  array{name: string, email: string, role: string, language: string}  $data
     */
    public function execute(array $data, User $createdBy): array
    {
        $tempPassword = Str::random(14);

        return DB::transaction(function () use ($data, $createdBy, $tempPassword) {
            $staff = User::create([
                'tenant_id' => $createdBy->tenant_id,
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $tempPassword, // 'hashed' cast hashes it
                'role'      => $data['role'],
                'language'  => $data['language'],
                'must_change_password' => true,
            ]);

            AuditLog::create([
                'tenant_id'    => $createdBy->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) $createdBy->id,
                'action'       => 'staff.created',
                'subject_type' => 'user',
                'subject_id'   => (string) $staff->id,
                'detail'       => ['role' => $staff->role, 'name' => $staff->name],
            ]);

            return [
                'staff'         => $staff,
                'staff_email'   => $staff->email,
                'temp_password' => $tempPassword,
            ];
        });
    }
}