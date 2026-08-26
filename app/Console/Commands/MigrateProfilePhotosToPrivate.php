<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Console\Command;
use Throwable;

class MigrateProfilePhotosToPrivate extends Command
{
    protected $signature = 'profile-photos:migrate-private';

    protected $description =
        'Reprocess legacy profile photos and move them to private storage';

    public function handle(
        ProfilePhotoService $photos
    ): int {
        $migrated = 0;
        $alreadyPrivate = 0;
        $missing = 0;
        $failed = 0;

        User::query()
            ->whereNotNull('profile_picture')
            ->select(['id', 'profile_picture'])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($users) use (
                    $photos,
                    &$migrated,
                    &$alreadyPrivate,
                    &$missing,
                    &$failed
                ): void {
                    foreach ($users as $user) {
                        $oldPath = $user->profile_picture;

                        if ($photos->privateExists($oldPath)) {
                            $photos->deleteLegacyPublic(
                                $oldPath
                            );

                            $alreadyPrivate++;

                            continue;
                        }

                        if (! $photos->legacyExists($oldPath)) {
                            $missing++;

                            $this->warn(
                                "User {$user->id}: file not found"
                            );

                            continue;
                        }

                        $newPath = null;

                        try {
                            $newPath = $photos->migrateLegacy(
                                $oldPath
                            );

                            $user->forceFill([
                                'profile_picture' => $newPath,
                            ])->save();

                            $photos->deleteLegacyPublic(
                                $oldPath
                            );

                            $migrated++;
                        } catch (Throwable $exception) {
                            if ($newPath !== null) {
                                $photos->delete($newPath);
                            }

                            report($exception);
                            $failed++;

                            $this->error(
                                "User {$user->id}: migration failed"
                            );
                        }
                    }
                }
            );

        $this->table(
            ['migrated', 'already private', 'missing', 'failed'],
            [[
                $migrated,
                $alreadyPrivate,
                $missing,
                $failed,
            ]]
        );

        return $failed === 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}
