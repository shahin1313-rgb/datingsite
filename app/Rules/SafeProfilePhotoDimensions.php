<?php

namespace App\Rules;

use App\Services\ProfilePhotoService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

final class SafeProfilePhotoDimensions implements ValidationRule
{
    /**
     * Reject decompression-bomb style images before GD decodes them.
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        /*
         * File type, upload status and ordinary dimensions are handled by
         * Laravel's preceding validation rules. This rule only adds a total
         * pixel budget, which max_width/max_height cannot express.
         */
        if (
            ! $value instanceof UploadedFile
            || ! $value->isValid()
        ) {
            return;
        }

        $path = $value->getRealPath();

        if (! is_string($path) || $path === '') {
            return;
        }

        $imageInfo = @getimagesize($path);

        if ($imageInfo === false) {
            return;
        }

        $width = (int) ($imageInfo[0] ?? 0);
        $height = (int) ($imageInfo[1] ?? 0);

        if ($width < 1 || $height < 1) {
            return;
        }

        /*
         * Division avoids an integer overflow if this rule is ever reused
         * with image formats whose dimensions exceed today's 4096px cap.
         */
        if (
            $width > intdiv(
                ProfilePhotoService::MAX_SOURCE_PIXELS,
                $height
            )
        ) {
            $fail(
                'مجموع ابعاد تصویر نباید بیشتر از ۶ مگاپیکسل باشد.'
            );
        }
    }
}
