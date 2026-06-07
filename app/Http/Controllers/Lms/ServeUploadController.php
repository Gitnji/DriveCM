<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\StudentPayment;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ServeUploadController extends Controller
{
    public function show(Upload $upload): Response
    {
        // BelongsToTenant scope on Upload model already 404s cross-tenant access.
        // P5 — payment screenshots get strict per-user auth (owning student OR
        // owner OR secretary). Lesson images stay accessible to any authenticated
        // tenant user (deferred as v1.1 hardening).
        $payment = StudentPayment::where('screenshot_upload_id', $upload->id)->first();
        if ($payment) {
            $user = Auth::guard('web')->user();
            $isOwningStudent = $user && (int) $user->id === (int) $payment->student_id;
            $isReviewer      = $user && ($user->isOwner() || $user->isSecretary());

            if (! $isOwningStudent && ! $isReviewer) {
                abort(403);
            }
        }

        if (! Storage::disk('local')->exists($upload->path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('local')->path($upload->path),
            ['Content-Type' => $upload->mime]
        );
    }
}