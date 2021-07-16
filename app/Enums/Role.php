<?php

namespace App\Enums;

class Role
{
    public const ADMIN = 1;
    public const USER = 2;
    public const NINJA = 3;
    public const MANAGER = 4;
    public const CUTTER = 5;
    public const EXTERNAL_USER = 6;

    public static function allRoles(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
