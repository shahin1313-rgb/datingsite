<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    public function show(
        Request $request,
        User $user,
        ProfilePhotoService $photos
    ): StreamedResponse {
        $viewer = $request->user();

        if (
            ! $viewer->isAdmin()
            && $viewer->id !== $user->id
        ) {
            $visible = User::query()
                ->publicMembers()
                ->notBlockedWith($viewer)
                ->whereKey($user->id)
                ->exists();

            abort_unless($visible, 404);
        }

        $path = $user->profile_picture;

        abort_unless(
            $photos->privateExists($path),
            404
        );

        return Storage::disk('local')->response(
            $path,
            null,
            [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' =>
                    'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
                'Content-Disposition' => 'inline',
            ]
        );
    }
}
