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
        $credentials = session('credentials');
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

    /**
     * DASH-1d / D162 — export all tenant schools as CSV.
     * Filename: drivecm-schools-{YYYY-MM-DD}.csv. Streamed (lazy chunks of 200).
     * UTF-8 BOM prepended so Excel on Windows reads accented school names correctly.
     */
    public function exportCsv()
    {
        $filename = 'drivecm-schools-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        $columns = [
            'id', 'name', 'subdomain', 'status',
            'contact_name', 'contact_email', 'contact_phone',
            'applicant_town', 'created_at', 'updated_at',
        ];

        return response()->streamDownload(function () use ($columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns);

            Tenant::orderBy('created_at', 'desc')
                ->lazy(200)
                ->each(function ($t) use ($out, $columns) {
                    $row = [];
                    foreach ($columns as $c) {
                        $row[] = (string) ($t->{$c} ?? '');
                    }
                    fputcsv($out, $row);
                });

            fclose($out);
        }, $filename, $headers);
    }
}