<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentPaymentSubmitRequest;
use App\Models\AuditLog;
use App\Models\PaymentType;
use App\Models\StudentPayment;
use App\Models\Tenant;
use App\Models\Upload;
use App\Services\PaymentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stancl\Tenancy\Tenancy;

class StudentPaymentController extends Controller
{
    public function index(PaymentStatus $paymentStatus)
    {
        $student = Auth::guard('web')->user();
        $tenant = app(Tenancy::class)->tenant;

        // Required types this student needs to engage with (threshold crossed).
        // The list includes types where they've submitted/been rejected — UX shows
        // the current status per type.
        $pendingRequired = $paymentStatus->pendingRequiredPayments($student);

        // All payments by this student (history).
        $allPayments = StudentPayment::where('student_id', $student->id)
            ->with(['paymentType', 'screenshot', 'reviewer:id,name'])
            ->orderByDesc('created_at')
            ->get();

        // Group by payment_type_id for quick lookup in the view.
        $paymentsByType = $allPayments->groupBy('payment_type_id');

        // Optional types catalog (active, not required) — student may opt in voluntarily.
        $optionalTypes = PaymentType::where('is_active', true)
            ->where('is_required', false)
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('student.payments.index', [
            'tenant'           => $tenant,
            'pendingRequired'  => $pendingRequired,
            'allPayments'      => $allPayments,
            'paymentsByType'   => $paymentsByType,
            'optionalTypes'    => $optionalTypes,
        ]);
    }

    public function submit(StudentPaymentSubmitRequest $request)
    {
        $student = Auth::guard('web')->user();
        $tenantId = $student->tenant_id;

        $type = PaymentType::where('is_active', true)
            ->findOrFail($request->input('payment_type_id'));

        // Store the file to payments/{tenant_id}/{uuid}.{ext} on the private local disk.
        $file = $request->file('screenshot');
        $ext = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $ext;
        $dir = 'payments/' . $tenantId;
        $path = $file->storeAs($dir, $filename, 'local');

        DB::transaction(function () use ($request, $student, $tenantId, $type, $file, $path) {
            // 1. Create Upload row.
            $upload = Upload::create([
                'tenant_id'     => $tenantId,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime'          => $file->getMimeType(),
                'size'          => $file->getSize(),
                'uploaded_by'   => $student->id,
            ]);

            // 2. Create StudentPayment. Amount snapshot from the type's current value.
            $payment = StudentPayment::create([
                'tenant_id'            => $tenantId,
                'student_id'           => $student->id,
                'payment_type_id'      => $type->id,
                'status'               => 'pending_review',
                'screenshot_upload_id' => $upload->id,
                'amount_xaf'           => $type->amount_xaf,
                'notes'                => $request->input('notes'),
                'created_via'          => 'student_upload',
                'submitted_at'         => now(),
            ]);

            // 3. Audit log.
            AuditLog::create([
                'tenant_id'    => $tenantId,
                'actor_type'   => 'user',
                'actor_id'     => (string) $student->id,
                'action'       => 'student_payment.submitted',
                'subject_type' => 'student_payment',
                'subject_id'   => (string) $payment->id,
                'detail'       => [
                    'payment_type_id' => $type->id,
                    'amount_xaf'      => $type->amount_xaf,
                ],
            ]);
        });

        return redirect()->route('student.payments.index')
            ->with('status', 'Payment proof submitted. The school will review and confirm.');
    }
}