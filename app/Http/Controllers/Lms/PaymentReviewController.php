<?php

namespace App\Http\Controllers\Lms;

use App\Actions\Tenant\ApproveStudentPayment;
use App\Actions\Tenant\ManualMarkStudentPayment;
use App\Actions\Tenant\RejectStudentPayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ManualMarkStudentPaymentRequest;
use App\Http\Requests\Tenant\RejectStudentPaymentRequest;
use App\Models\PaymentType;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PaymentReviewController extends Controller
{
    public function index()
    {
        $pending = StudentPayment::where('status', 'pending_review')
            ->with(['student:id,name,email', 'paymentType:id,name', 'screenshot'])
            ->orderBy('submitted_at')
            ->get();

        $approvedCount = StudentPayment::where('status', 'approved')->count();
        $rejectedCount = StudentPayment::where('status', 'rejected')->count();

        return view('lms.payments.reviews.index', [
            'pending'       => $pending,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    public function show(StudentPayment $payment)
    {
        $payment->load(['student:id,name,email,phone,town', 'paymentType', 'screenshot', 'reviewer:id,name']);

        return view('lms.payments.reviews.show', ['payment' => $payment]);
    }

    public function approve(StudentPayment $payment, ApproveStudentPayment $action)
    {
        $action->execute($payment, Auth::guard('web')->user());

        return redirect()->route('lms.payment-reviews.index')
            ->with('status', 'Payment approved. Student can now access lessons.');
    }

    public function reject(RejectStudentPaymentRequest $request, StudentPayment $payment, RejectStudentPayment $action)
    {
        $action->execute($payment, Auth::guard('web')->user(), $request->input('rejection_reason'));

        return redirect()->route('lms.payment-reviews.index')
            ->with('status', 'Payment rejected. Student will see the rejection on their Payments page.');
    }

    public function manualCreate()
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        $types = PaymentType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('lms.payments.reviews.manual-create', [
            'students' => $students,
            'types'    => $types,
        ]);
    }

    public function manualStore(ManualMarkStudentPaymentRequest $request, ManualMarkStudentPayment $action)
    {
        $student = User::findOrFail($request->input('student_id'));
        $type = PaymentType::findOrFail($request->input('payment_type_id'));

        $action->execute($student, $type, Auth::guard('web')->user(), $request->input('notes'));

        return redirect()->route('lms.payment-reviews.index')
            ->with('status', 'Manual payment recorded as approved.');
    }
}