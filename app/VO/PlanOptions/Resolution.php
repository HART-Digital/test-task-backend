<?php

namespace App\VO\PlanOptions;

use InvalidArgumentException;

final class Resolution
{
    public const MIN = 512;
    public const MAX = 4096;
    public const DEFAULT = 2048;

    private $resolution;

    private function __construct(int $resolution)
    {
        if ($resolution < self::MIN || $resolution > self::MAX) {
            throw new InvalidArgumentException("Invalid Resolution: {$resolution}");
        }

        $this->resolution = $resolution;
    }

    public static function create(?int $resolution): Resolution
    {
        if ($resolution === null) {
            $resolution = self::DEFAULT;
        }

        return new static($resolution);
    }

    public function value(): int
    {
        return $this->resolution;
    }

    public function equals(Resolution $resolution): bool
    {
        return $this->resolution === $resolution->value();
    }
}
