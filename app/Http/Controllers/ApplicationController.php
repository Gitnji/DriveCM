<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyForSchoolRequest;
use App\Models\Tenant;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('apply.form');
    }

    public function store(ApplyForSchoolRequest $request)
    {
        $data = $request->validated();

        Tenant::create([
            'name' => $data['school_name'],
            'status' => 'pending',
            'desired_subdomain' => $data['desired_subdomain'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'applicant_town' => $data['applicant_town'],
            'submitted_at' => now(),
            // subdomain stays null until approval (D96)
            // tenant id (UUID) auto-set by the model's creating hook
        ]);

        return redirect()->route('apply.submitted');
    }

    public function submitted()
    {
        return view('apply.submitted');
    }

    /**
     * DASH-1d / D162 — export all tenant schools as CSV.
     * Filename: drivecm-schools-{YYYY-MM-DD}.csv.
     * Streamed to keep memory bounded if the list grows large.
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
            // BOM so Excel reads UTF-8 correctly on Windows
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