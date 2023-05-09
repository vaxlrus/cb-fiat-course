<?php

namespace App\Domain\Entities;

final class Currency
{
    public function __construct(
        public readonly int $numCode,
        public readonly string $charCode,
        public readonly int $nominal,
        public readonly string $name,
        public readonly float $value
    )
    {
        //
    }
}
