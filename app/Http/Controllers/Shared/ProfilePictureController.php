<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;

class ProfilePictureController extends Controller
{
    public function show(string $filename)
    {
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
            abort(404);
        }

        $relativePath = 'uploads/profile_picture/' . $filename;

        $legacyPath = public_path($relativePath);
        if (is_file($legacyPath)) {
            return response()->file($legacyPath);
        }

        $storagePublicPath = storage_path('app/public/' . $relativePath);
        if (is_file($storagePublicPath)) {
            return response()->file($storagePublicPath);
        }

        abort(404);
    }
}
