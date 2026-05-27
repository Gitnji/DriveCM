<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ServeUploadController extends Controller
{
    public function show(Upload $upload): Response
    {
        // The BelongsToTenant global scope already means route-model binding
        // would 404 an upload from another tenant. This is the same protection
        // the lesson routes rely on — a foreign upload id never resolves.

        if (! Storage::disk('local')->exists($upload->path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('local')->path($upload->path),
            ['Content-Type' => $upload->mime]
        );
    }
}