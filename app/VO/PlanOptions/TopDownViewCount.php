<?php

namespace App\VO\PlanOptions;

use InvalidArgumentException;

final class TopDownViewCount
{
    public const MIN = 1;
    public const MAX = 60;
    public const DEFAULT = 1;

    private $count;

    private function __construct(int $count)
    {
        if ($count < self::MIN || $count > self::MAX) {
            throw new InvalidArgumentException("Invalid TopDownViewCount: {$count}");
        }

        $this->count = $count;
    }

    public static function create(?int $count)
    {
        if ($count === null) {
            $count = self::DEFAULT;
        }

        return new static($count);
    }

    public function value(): int
    {
        return $this->count;
    }

    public function equals(TopDownViewCount $topDownViewCount): bool
    {
        return $this->count === $topDownViewCount->value();
    }
}
