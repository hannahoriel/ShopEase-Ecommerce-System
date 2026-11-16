<?php

namespace App\Rules;

use Closure;
use Illuminate\Http\UploadedFile;

final class DocumentUploadSize
{
    public static function limit(int $pdfLimitKb): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail) use ($pdfLimitKb): void {
            if (! $value instanceof UploadedFile || ! $value->isValid()) {
                return;
            }

            $isPdf = $value->getMimeType() === 'application/pdf';
            $limitKb = $isPdf ? $pdfLimitKb : 15360;

            if ($value->getSize() > $limitKb * 1024) {
                $limitMb = $limitKb / 1024;
                $type = $isPdf ? 'PDF' : 'image';
                $fail("The :attribute {$type} must be {$limitMb} MB or smaller.");
            }
        };
    }
}
