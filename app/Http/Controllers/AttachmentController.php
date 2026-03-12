<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Securely serve an attachment.
     */
    public function show(Request $request, Attachment $attachment)
    {
        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        $headers = [];
        if ($attachment->mime_type) {
            $headers['Content-Type'] = $attachment->mime_type;
        }

        // return as download if requested, otherwise view inline
        if ($request->has('download')) {
            return Storage::download($attachment->file_path, $attachment->filename, $headers);
        }

        return Storage::response($attachment->file_path, $attachment->filename, $headers);
    }
}
