<?php

namespace App\Http\Controllers;

use App\Models\Block;
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
            $blocked = Block::query()
                ->where(function ($query) use (
                    $viewer,
                    $user
                ): void {
                    $query
                        ->where(
                            'blocker_id',
                            $viewer->id
                        )
                        ->where(
                            'blocked_id',
                            $user->id
                        );
                })
                ->orWhere(function ($query) use (
                    $viewer,
                    $user
                ): void {
                    $query
                        ->where(
                            'blocker_id',
                            $user->id
                        )
                        ->where(
                            'blocked_id',
                            $viewer->id
                        );
                })
                ->exists();

            abort_if($blocked, 404);
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
