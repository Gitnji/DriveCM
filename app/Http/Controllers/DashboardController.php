<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function tenant()
    {
        return view('dashboard.tenant', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function admin()
    {
        return view('dashboard.admin', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }
}