<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreUploadRequest;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(StoreUploadRequest $request)
    {
        $tenantId = session('tenant_id');
        $file = $request->file('image');

        // Tenant-isolated path (D54): lessons/{tenant_id}/{random}.{ext}
        $ext = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $ext;
        $dir = 'lessons/' . $tenantId;

        // Store on the private 'local' disk — NOT 'public' (D54: access-checked, not public).
        $path = $file->storeAs($dir, $filename, 'local');

        $upload = Upload::create([
            'tenant_id' => $tenantId,           // also auto-filled by trait; explicit for clarity
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => Auth::guard('web')->id(),
        ]);

        // Return the access-checked URL the block editor will store in the image block.
        return response()->json([
            'id' => $upload->id,
            'url' => route('lms.uploads.show', $upload),
        ]);
    }
}