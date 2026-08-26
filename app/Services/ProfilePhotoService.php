<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfilePhotoService
{
    private const DIRECTORY = 'profile_pictures';

    private const MAX_SOURCE_DIMENSION = 4096;

    private const MAX_OUTPUT_DIMENSION = 1600;

    /**
     * Decode and re-encode an uploaded image before storing it.
     *
     * Re-encoding prevents EXIF/IPTC/GPS metadata and the original
     * uploaded bytes from reaching other users.
     */
    public function store(UploadedFile $photo): string
    {
        $sourcePath = $photo->getRealPath();

        if ($sourcePath === false) {
            $this->invalidPhoto();
        }

        $contents = @file_get_contents($sourcePath);

        if ($contents === false) {
            $this->invalidPhoto();
        }

        return $this->storeSanitized(
            $contents,
            $sourcePath
        );
    }

    /**
     * Reprocess an existing public profile photo into private storage.
     */
    public function migrateLegacy(string $path): string
    {
        $sourcePath = $this->legacySourcePath($path);

        if ($sourcePath === null) {
            $this->invalidPhoto(
                'فایل تصویر قدیمی پیدا نشد.'
            );
        }

        $contents = @file_get_contents($sourcePath);

        if ($contents === false) {
            $this->invalidPhoto(
                'خواندن تصویر قدیمی ممکن نیست.'
            );
        }

        return $this->storeSanitized(
            $contents,
            $sourcePath
        );
    }

    public function privateExists(?string $path): bool
    {
        return $this->isManagedPath($path)
            && Storage::disk('local')->exists($path);
    }

    public function legacyExists(?string $path): bool
    {
        return $this->isManagedPath($path)
            && $this->legacySourcePath($path) !== null;
    }

    /**
     * Delete both the private file and any legacy public copy.
     */
    public function delete(?string $path): void
    {
        if (! $this->isManagedPath($path)) {
            return;
        }

        Storage::disk('local')->delete($path);
        $this->deleteLegacyPublic($path);
    }

    /**
     * Remove only public copies after a successful database update.
     */
    public function deleteLegacyPublic(?string $path): void
    {
        if (! $this->isManagedPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);

        /*
         * Some Windows/ZIP deployments contain a copied public/storage
         * directory instead of a symbolic link. Remove that exact copy too.
         */
        $publicStorageRoot = realpath(
            public_path('storage')
        );

        $publicCopy = public_path(
            'storage/'.$path
        );

        $realPublicCopy = realpath($publicCopy);

        if (
            $publicStorageRoot !== false
            && $realPublicCopy !== false
            && str_starts_with(
                $realPublicCopy,
                $publicStorageRoot.DIRECTORY_SEPARATOR
            )
            && is_file($realPublicCopy)
        ) {
            @unlink($realPublicCopy);
        }
    }

    private function storeSanitized(
        string $contents,
        string $sourcePath
    ): string {
        $sanitized = $this->encodeAsJpeg(
            $contents,
            $sourcePath
        );

        $path = self::DIRECTORY.'/'
            .Str::uuid()->toString().'.jpg';

        $stored = Storage::disk('local')->put(
            $path,
            $sanitized
        );

        if (! $stored) {
            $this->invalidPhoto(
                'ذخیره امن تصویر انجام نشد.'
            );
        }

        return $path;
    }

    private function encodeAsJpeg(
        string $contents,
        string $sourcePath
    ): string {
        if (! extension_loaded('gd')) {
            $this->invalidPhoto(
                'افزونه GD برای پردازش امن تصویر فعال نیست.'
            );
        }

        $imageInfo = @getimagesizefromstring($contents);

        if (
            $imageInfo === false
            || $imageInfo[0] < 1
            || $imageInfo[1] < 1
            || $imageInfo[0] > self::MAX_SOURCE_DIMENSION
            || $imageInfo[1] > self::MAX_SOURCE_DIMENSION
        ) {
            $this->invalidPhoto(
                'ابعاد تصویر نامعتبر یا بیش از حد مجاز است.'
            );
        }

        $image = @imagecreatefromstring($contents);

        if (! $image instanceof GdImage) {
            $this->invalidPhoto();
        }

        try {
            if ($imageInfo['mime'] === 'image/jpeg') {
                $image = $this->applyExifOrientation(
                    $image,
                    $sourcePath
                );
            }

            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);
            $scale = min(
                1,
                self::MAX_OUTPUT_DIMENSION
                    / max($sourceWidth, $sourceHeight)
            );

            $targetWidth = max(
                1,
                (int) round($sourceWidth * $scale)
            );

            $targetHeight = max(
                1,
                (int) round($sourceHeight * $scale)
            );

            $canvas = imagecreatetruecolor(
                $targetWidth,
                $targetHeight
            );

            if (! $canvas instanceof GdImage) {
                $this->invalidPhoto();
            }

            try {
                $white = imagecolorallocate(
                    $canvas,
                    255,
                    255,
                    255
                );

                imagefill($canvas, 0, 0, $white);

                $copied = imagecopyresampled(
                    $canvas,
                    $image,
                    0,
                    0,
                    0,
                    0,
                    $targetWidth,
                    $targetHeight,
                    $sourceWidth,
                    $sourceHeight
                );

                if (! $copied) {
                    $this->invalidPhoto();
                }

                ob_start();

                try {
                    $encoded = imagejpeg(
                        $canvas,
                        null,
                        88
                    );

                    $output = ob_get_contents();
                } finally {
                    ob_end_clean();
                }

                if (
                    ! $encoded
                    || ! is_string($output)
                    || $output === ''
                ) {
                    $this->invalidPhoto();
                }

                return $output;
            } finally {
                imagedestroy($canvas);
            }
        } finally {
            imagedestroy($image);
        }
    }

    private function applyExifOrientation(
        GdImage $image,
        string $sourcePath
    ): GdImage {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data(
            $sourcePath,
            'IFD0',
            true
        );

        if (! is_array($exif)) {
            return $image;
        }

        $orientation = (int) (
            $exif['IFD0']['Orientation']
            ?? $exif['Orientation']
            ?? 1
        );

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip(
                $image,
                in_array($orientation, [2, 5], true)
                    ? IMG_FLIP_HORIZONTAL
                    : IMG_FLIP_VERTICAL
            );
        }

        $angle = match ($orientation) {
            3 => 180,
            5, 6 => -90,
            7, 8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate(
            $image,
            $angle,
            0
        );

        if (! $rotated instanceof GdImage) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }

    private function legacySourcePath(
        string $path
    ): ?string {
        if (! $this->isManagedPath($path)) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        $publicCopy = public_path(
            'storage/'.$path
        );

        return is_file($publicCopy)
            ? $publicCopy
            : null;
    }

    private function isManagedPath(
        ?string $path
    ): bool {
        return is_string($path)
            && preg_match(
                '#\Aprofile_pictures/[A-Za-z0-9._-]+\z#',
                $path
            ) === 1;
    }

    private function invalidPhoto(
        string $message = 'تصویر قابل پردازش نیست.'
    ): never {
        throw ValidationException::withMessages([
            'profile_picture' => $message,
        ]);
    }
}
