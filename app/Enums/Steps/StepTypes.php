<?php

namespace App\Enums\Steps;

final class StepTypes
{
    public const PLAN = 'plan';
    public const MASK = 'mask';
    public const NEURAL = 'neural';
    public const FURNITURE = 'furniture';
    public const UNREAL = 'unreal';
    public const LOGS = 'logs';

    public static function isPlan(string $type): bool
    {
        return $type === self::PLAN;
    }

    public static function isMask(string $type): bool
    {
        return $type === self::MASK;
    }

    public static function isNeural(string $type): bool
    {
        return $type === self::NEURAL;
    }

    public static function isFurniture(string $type): bool
    {
        return $type === self::FURNITURE;
    }

    public static function isUnreal(string $type): bool
    {
        return $type === self::UNREAL;
    }

    public static function isLogs(string $type): bool
    {
        return $type === self::LOGS;
    }
}
