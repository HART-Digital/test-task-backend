<?php

namespace App\VO\PlanOptions;

use InvalidArgumentException;

final class Styles
{
    public const DEFAULT = 0;

    private $styles;

    private function __construct(array $styles)
    {
        foreach (filter_var_array($styles, FILTER_VALIDATE_INT) as $style) {
            if ($style === false) {
                throw new InvalidArgumentException("Style {$style} is not valid");
            }
        }

        sort($styles);

        $this->styles = implode('|', $styles);
    }

    public static function create(?array $styles)
    {
        if ($styles === null) {
            $styles = [self::DEFAULT];
        }

        return new static($styles);
    }

    public function value(): string
    {
        return $this->styles;
    }

    public function equals(Styles $styles): bool
    {
        return $this->styles === $styles->value();
    }
}
