<?php

namespace App\Traits;

trait EnumTryFromName
{
    public static function tryFromName(string $name): ?static
    {
        $enum = new \ReflectionEnum(static::class);

        return $enum->hasCase($name) ? $enum->getCase($name)->getValue() : null;
    }
}
