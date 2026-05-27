<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ApproveApplication;
use App\Actions\Admin\RejectApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveApplicationRequest;
use App\Http\Requests\Admin\RejectApplicationRequest;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $pending = Tenant::where('status', 'pending')
            ->orderByDesc('submitted_at')
            ->get();

        $recent = Tenant::whereIn('status', ['active', 'rejected'])
            ->orderByDesc('reviewed_at')
            ->limit(10)
            ->get();

        return view('admin.applications.index', [
            'pending' => $pending,
            'recent' => $recent,
        ]);
    }

    public function show(Tenant $tenant)
    {
        return view('admin.applications.show', ['tenant' => $tenant]);
    }

    public function approve(ApproveApplicationRequest $request, Tenant $tenant, ApproveApplication $action)
    {
        abort_unless($tenant->status === 'pending', 404);

        $result = $action->execute(
            $tenant,
            $request->validated()['subdomain'],
            Auth::guard('admin')->user()
        );

        // Flash the credentials to the SUCCESS VIEW once (D98).
        return redirect()
            ->route('admin.applications.approved', $tenant)
            ->with('credentials', [
                'email' => $result['owner_email'],
                'password' => $result['temp_password'],
            ]);
    }

    public function approved(Tenant $tenant)
    {
        // Read the one-shot credentials flash. If the admin refreshes, it's gone.
        $credentials = session('credentials'); // session()->reflash() is NOT done — single-view only
        if (! $credentials) {
            return redirect()->route('admin.applications.index');
        }

        return view('admin.applications.approved', [
            'tenant' => $tenant,
            'credentials' => $credentials,
        ]);
    }

    public function reject(RejectApplicationRequest $request, Tenant $tenant, RejectApplication $action)
    {
        abort_unless($tenant->status === 'pending', 404);

        $action->execute(
            $tenant,
            $request->validated()['rejection_reason'] ?? null,
            Auth::guard('admin')->user()
        );

        return redirect()->route('admin.applications.index')
            ->with('status', __('Application rejected.'));
    }
}