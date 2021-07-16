<?php

namespace App\VO\PlanOptions;

use InvalidArgumentException;

final class MiddleCutHeight
{
    public const MIN = 0;
    public const MAX = 280;
    public const DEFAULT = 150;

    private $height;

    private function __construct(int $height)
    {
        if ($height < self::MIN || $height > self::MAX) {
            throw new InvalidArgumentException("Invalid Middle Cut Height: {$height}");
        }

        $this->height = $height;
    }

    public static function create(?int $height): MiddleCutHeight
    {
        if ($height === null) {
            $height = self::DEFAULT;
        }

        return new static($height);
    }

    public function value(): int
    {
        return $this->height;
    }

    public function equals(MiddleCutHeight $height): bool
    {
        return $this->height === $height->value();
    }
}
