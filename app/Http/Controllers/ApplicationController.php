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
}