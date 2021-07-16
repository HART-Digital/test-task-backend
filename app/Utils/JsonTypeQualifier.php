<?php

namespace App\Utils;

use App\Enums\Steps\StepTypes;
use Illuminate\Http\UploadedFile;

final class JsonTypeQualifier
{
    public static function qualifyUploadedFile(UploadedFile $file): string
    {
        $content = file_get_contents($file->getRealPath());
        return self::qualifyContent($content);
    }

    public static function qualifyContent(string $content): string
    {
        if (str_contains($content, 'Furniture')) {
            return StepTypes::FURNITURE;
        }

        return StepTypes::NEURAL;
    }
}
