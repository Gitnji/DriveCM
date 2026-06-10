<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyForSchoolRequest;
use App\Models\PlatformSetting;
use App\Models\Tenant;

class ApplicationController extends Controller
{
    /**
     * FB2 — Public pricing/features page. Shown before /apply so schools see fees + free
     * trial + features before applying.
     */
    public function pricing()
    {
        return view('apply.pricing', [
            'settings' => PlatformSetting::current(),
        ]);
    }

    public function create()
    {
        return view('apply.form', [
            'settings' => PlatformSetting::current(),
        ]);
    }

    public function store(ApplyForSchoolRequest $request)
    {
        $data = $request->validated();
        $now = now();

        // FB2 — bypass Eloquent save events. The stancl InvalidatesTenantsResolverCache
        // trait fires on the 'saved' Eloquent event and chokes on a null tenant during
        // apex-context tenant creation. Insert via query builder, manually generating
        // the UUID that the model's creating hook would normally set.
        \Illuminate\Support\Facades\DB::connection(config('tenancy.database.central_connection', 'pgsql'))
            ->table('tenants')
            ->insert([
                'id'                => (string) \Illuminate\Support\Str::uuid(),
                'name'              => $data['school_name'],
                'status'            => 'pending',
                'desired_subdomain' => $data['desired_subdomain'],
                'contact_name'      => $data['contact_name'],
                'contact_email'     => $data['contact_email'],
                'contact_phone'     => $data['contact_phone'] ?? null,
                'applicant_town'    => $data['applicant_town'],
                'submitted_at'      => $now,
                'terms_agreed_at'   => $now,
                // FB1 — billing fields seeded with defaults; ApproveApplication sets them properly on approval.
                'billing_status'    => 'active',
                'created_at'        => $now,
                'updated_at'        => $now,
                // stancl `data` column (JSON for arbitrary tenant data) — required.
                'data'              => json_encode([]),
            ]);

        return redirect()->route('apply.submitted');
    }

    public function submitted()
    {
        return view('apply.submitted');
    }

    /**
     * DASH-1d / D162 — export all tenant schools as CSV.
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