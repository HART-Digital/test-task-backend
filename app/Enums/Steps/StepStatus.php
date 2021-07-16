<?php

namespace App\Enums\Steps;

class StepStatus
{
    public const ERROR = -1;
    public const WAIT = 0;
    public const PROCESS = 1;
    public const FINISH = 2;

    public static function all(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
