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

    public const MAX_SOURCE_PIXELS = 6_000_000;

    private const MAX_OUTPUT_DIMENSION = 1600;

    /*
     * GD commonly needs more than the raw four bytes per pixel while
     * decoding/resampling. A conservative estimate lets us fail with a
     * validation error before PHP reaches an uncatchable memory exhaustion.
     */
    private const ESTIMATED_GD_BYTES_PER_PIXEL = 8;

    private const GD_MEMORY_RESERVE_BYTES = 16 * 1024 * 1024;

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

        if ($imageInfo === false) {
            $this->invalidPhoto(
                'ابعاد تصویر نامعتبر یا بیش از حد مجاز است.'
            );
        }

        $sourceWidth = (int) ($imageInfo[0] ?? 0);
        $sourceHeight = (int) ($imageInfo[1] ?? 0);

        if (
            $sourceWidth < 1
            || $sourceHeight < 1
            || $sourceWidth > self::MAX_SOURCE_DIMENSION
            || $sourceHeight > self::MAX_SOURCE_DIMENSION
        ) {
            $this->invalidPhoto(
                'ابعاد تصویر نامعتبر یا بیش از حد مجاز است.'
            );
        }

        if (
            $sourceWidth > intdiv(
                self::MAX_SOURCE_PIXELS,
                $sourceHeight
            )
        ) {
            $this->invalidPhoto(
                'مجموع ابعاد تصویر نباید بیشتر از ۶ مگاپیکسل باشد.'
            );
        }

        $orientation = $imageInfo['mime'] === 'image/jpeg'
            ? $this->readExifOrientation($sourcePath)
            : 1;

        $this->assertGdMemoryBudget(
            $sourceWidth,
            $sourceHeight,
            $orientation
        );

        $image = @imagecreatefromstring($contents);

        if (! $image instanceof GdImage) {
            $this->invalidPhoto();
        }

        try {
            if ($imageInfo['mime'] === 'image/jpeg') {
                $image = $this->applyExifOrientation(
                    $image,
                    $orientation
                );
            }

            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);

            [$targetWidth, $targetHeight] =
                $this->scaledDimensions(
                    $sourceWidth,
                    $sourceHeight
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
        int $orientation
    ): GdImage {
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

    private function readExifOrientation(
        string $sourcePath
    ): int {
        if (! function_exists('exif_read_data')) {
            return 1;
        }

        $exif = @exif_read_data(
            $sourcePath,
            'IFD0',
            true
        );

        if (! is_array($exif)) {
            return 1;
        }

        $orientation = (int) (
            $exif['IFD0']['Orientation']
            ?? $exif['Orientation']
            ?? 1
        );

        return in_array(
            $orientation,
            range(1, 8),
            true
        ) ? $orientation : 1;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function scaledDimensions(
        int $sourceWidth,
        int $sourceHeight
    ): array {
        $scale = min(
            1,
            self::MAX_OUTPUT_DIMENSION
                / max($sourceWidth, $sourceHeight)
        );

        return [
            max(
                1,
                (int) round($sourceWidth * $scale)
            ),
            max(
                1,
                (int) round($sourceHeight * $scale)
            ),
        ];
    }

    private function assertGdMemoryBudget(
        int $sourceWidth,
        int $sourceHeight,
        int $orientation
    ): void {
        $memoryLimit = $this->memoryLimitInBytes();

        if ($memoryLimit === null) {
            return;
        }

        $rotationRequired = in_array(
            $orientation,
            [5, 6, 7, 8],
            true
        );

        $orientedWidth = $rotationRequired
            ? $sourceHeight
            : $sourceWidth;

        $orientedHeight = $rotationRequired
            ? $sourceWidth
            : $sourceHeight;

        [$targetWidth, $targetHeight] =
            $this->scaledDimensions(
                $orientedWidth,
                $orientedHeight
            );

        $sourceBuffer = $sourceWidth
            * $sourceHeight
            * self::ESTIMATED_GD_BYTES_PER_PIXEL;

        $rotationBuffer = $rotationRequired
            ? $sourceBuffer
            : 0;

        $targetBuffer = $targetWidth
            * $targetHeight
            * self::ESTIMATED_GD_BYTES_PER_PIXEL;

        $required = $sourceBuffer
            + $rotationBuffer
            + $targetBuffer
            + self::GD_MEMORY_RESERVE_BYTES;

        $available = max(
            0,
            $memoryLimit - memory_get_usage(true)
        );

        if ($required > $available) {
            $this->invalidPhoto(
                'ابعاد تصویر نسبت به حافظه امن پردازش بیش از حد بزرگ است.'
            );
        }
    }

    private function memoryLimitInBytes(): ?int
    {
        $value = trim((string) ini_get('memory_limit'));

        if ($value === '' || $value === '-1') {
            return null;
        }

        if (
            preg_match(
                '/\A(\d+)([KMG]?)\z/i',
                $value,
                $matches
            ) !== 1
        ) {
            return null;
        }

        $multiplier = match (strtoupper($matches[2])) {
            'G' => 1024 ** 3,
            'M' => 1024 ** 2,
            'K' => 1024,
            default => 1,
        };

        $bytes = (int) $matches[1] * $multiplier;

        return $bytes > 0 ? $bytes : null;
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
