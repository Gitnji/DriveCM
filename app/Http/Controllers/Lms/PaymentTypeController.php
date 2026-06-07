<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StorePaymentTypeRequest;
use App\Http\Requests\Tenant\UpdatePaymentTypeRequest;
use App\Models\AuditLog;
use App\Models\PaymentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $active = PaymentType::orderBy('sort_order')->orderBy('name')->get();
        $deleted = PaymentType::onlyTrashed()->orderByDesc('deleted_at')->get();

        return view('lms.payments.types.index', [
            'active'  => $active,
            'deleted' => $deleted,
        ]);
    }

    public function create()
    {
        return view('lms.payments.types.create');
    }

    public function store(StorePaymentTypeRequest $request)
    {
        $type = DB::transaction(function () use ($request) {
            $type = PaymentType::create($request->validated() + [
                'is_active' => true,
            ]);

            AuditLog::create([
                'tenant_id'    => $type->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'payment_type.created',
                'subject_type' => 'payment_type',
                'subject_id'   => (string) $type->id,
                'detail'       => ['name' => $type->name, 'amount_xaf' => $type->amount_xaf],
            ]);

            return $type;
        });

        return redirect()->route('lms.payment-types.index')
            ->with('status', 'Payment type "' . $type->name . '" created.');
    }

    public function edit(PaymentType $paymentType)
    {
        return view('lms.payments.types.edit', ['type' => $paymentType]);
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType)
    {
        DB::transaction(function () use ($request, $paymentType) {
            $paymentType->update($request->validated());

            AuditLog::create([
                'tenant_id'    => $paymentType->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'payment_type.updated',
                'subject_type' => 'payment_type',
                'subject_id'   => (string) $paymentType->id,
                'detail'       => ['changes' => array_keys($request->validated())],
            ]);
        });

        return redirect()->route('lms.payment-types.index')
            ->with('status', 'Payment type updated.');
    }

    public function destroy(PaymentType $paymentType)
    {
        DB::transaction(function () use ($paymentType) {
            $paymentType->delete(); // soft delete

            AuditLog::create([
                'tenant_id'    => $paymentType->tenant_id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'payment_type.removed',
                'subject_type' => 'payment_type',
                'subject_id'   => (string) $paymentType->id,
                'detail'       => ['name' => $paymentType->name],
            ]);
        });

        return redirect()->route('lms.payment-types.index')
            ->with('status', 'Payment type removed.');
    }

    public function restore(int $id)
    {
        $type = PaymentType::withTrashed()->findOrFail($id);
        $type->restore();

        AuditLog::create([
            'tenant_id'    => $type->tenant_id,
            'actor_type'   => 'user',
            'actor_id'     => (string) Auth::guard('web')->id(),
            'action'       => 'payment_type.restored',
            'subject_type' => 'payment_type',
            'subject_id'   => (string) $type->id,
            'detail'       => ['name' => $type->name],
        ]);

        return redirect()->route('lms.payment-types.index')
            ->with('status', 'Payment type restored.');
    }
}